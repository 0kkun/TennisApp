<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Services\FavoritePlayer\FavoritePlayerServiceInterface;

class FavoritePlayerController extends Controller
{
    private $players_repository;
    private $favorite_players_repository;
    private $favorite_player_service;


    /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repository
     */
    public function __construct(
        PlayersRepository $players_repository,
        FavoritePlayersRepository $favorite_players_repository,
        FavoritePlayerServiceInterface $favorite_player_service
    )
    {
        $this->players_repository = $players_repository;
        $this->favorite_players_repository = $favorite_players_repository;
        $this->favorite_player_service = $favorite_player_service;
    }


    /**
     * お気に入り選手登録画面
     *
     * @return void
     */
    public function index( Request $request )
    {
        // 入力された検索パラメータ取得
        $inputs = $request->all();

        $params = [
            'name'    => $inputs['name'] ?? '',
            'country' => $inputs['country'] ?? '',
            'age'     => $inputs['age'] ?? '',
        ];

        $players = $this->favorite_player_service->searchPlayers($inputs)->toArray();

        $favorite_player_ids = $this->favorite_players_repository->getAll()->pluck('player_id')->toArray();

        $player_lists = $this->makePlayerLists( $players, $favorite_player_ids );

        $country_names = $this->players_repository->getAllCountryNames();

        return view('favorite_player.index', compact('player_lists','params','country_names'));
    }


    /**
     * お気に入り選手登録メソッド
     *
     * @param Request $request
     * @return void
     */
    public function add( Request $request )
    {
        $data['player_id'] = $request->favorite_player_id;

        // バルクインサートで保存
        if ( !empty($data) ) {
            $this->favorite_players_repository->bulkInsertOrUpdate( $data );
        }

        session()->flash('flash_success', 'You added player!');

        return redirect()->route('favorite_player.index');
    }


    /**
     * お気に入り選手削除メソッド
     *
     * @param Request $request
     * @return void
     */
    public function remove( Request $request )
    {
        $favorite_player_id = $request->favorite_player_id;

        $this->favorite_players_repository->deleteRecord( $favorite_player_id );

        session()->flash('flash_alert', 'You removed player.');

        return redirect()->route('favorite_player.index');
    }


    /**
     * お気に入り選手に登録されている場合はフラグを立てつつ、一覧表示用のデータを作成
     *
     * @param array $players
     * @param array $favorite_players
     * @return array
     */
    private function makePlayerLists( array $players, array $favorite_player_ids ): array
    {
        $player_lists = array();

        foreach ( $players as $index => $player ) {

            $player_lists[$index]['id']              = $player['id'];
            $player_lists[$index]['name_jp']         = $player['name_jp'];
            $player_lists[$index]['name_en']         = $player['name_en'];
            $player_lists[$index]['country']         = $player['country'];
            $player_lists[$index]['wiki_url']        = $player['wiki_url'];
            $player_lists[$index]['age']             = $player['age'];
            $player_lists[$index]['favorite_status'] = 0;

            if ( count($favorite_player_ids) > 0 ) {
                for ( $i=0; $i<count($favorite_player_ids); $i++ ) {
                    if ( $player_lists[$index]['id'] === $favorite_player_ids[$i] ) {
                        $player_lists[$index]['favorite_status'] = 1;
                    }
                }
            }
        }

        $based_key = 'favorite_status';
        $player_lists = $this->sortByKey( $player_lists, $based_key );

        return $player_lists;
    }


    /**
     * 渡したキーのバリューに基づいて配列をソートする
     *
     * @param array $lists
     * @param string $key
     * @return array
     */
    private function sortByKey( array $lists, string $based_key ): array
    {
        // ここの配列宣言は必須。
        $sort = array();

        // ソート用の配列を用意
        foreach ( $lists as $key => $value ) {
            $sort[$key] = $value[$based_key];
        }

        array_multisort( $sort, SORT_DESC, $lists );

        return $lists;
    }
}
