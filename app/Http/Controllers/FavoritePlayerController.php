<?php

namespace App\Http\Controllers;

use App\Models\FavoritePlayer;
use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class FavoritePlayerController extends Controller
{
    private $players_repository;
    private $favorite_players_repository;

    /**
     * リポジトリをDI
     * 
     * @param PlayersRepository $players_repositor
     */
    public function __construct(
        PlayersRepository $players_repository,
        FavoritePlayersRepository $favorite_players_repository
    )
    {
        $this->players_repository = $players_repository;
        $this->favorite_players_repository = $favorite_players_repository;
    }


    /**
     * お気に入り選手登録画面
     *
     * @return void
     */
    public function index()
    {
        $players = $this->players_repository->getAll()->toArray();
        $favorite_player_ids = $this->favorite_players_repository->getAll()->pluck('player_id')->toArray();

        $player_lists = $this->makePlayerLists($players, $favorite_player_ids);

        // echo('<pre>');
        // var_dump($player_lists);
        // echo('<pre>');

        return view('favorite_player.index', compact('player_lists'));
    }


    /**
     * お気に入り選手登録メソッド
     *
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        $data['player_id'] = $request->favorite_player_id;

        // バルクインサートで保存
        if (!empty($data)) {
            $this->favorite_players_repository->bulkInsertOrUpdate($data);
        }

        return redirect()->route('favorite_player.index');
    }


    /**
     * お気に入り選手削除メソッド
     *
     * @param Request $request
     * @return void
     */
    public function remove(Request $request)
    {
        $favorite_player_id = $request->favorite_player_id;

        $this->favorite_players_repository->deleteRecord($favorite_player_id);

        return redirect()->route('favorite_player.index');
    }



    /**
     * お気に入り選手に登録されている場合はフラグを立てるメソッド
     * お気に入り選手を先頭にソートしてから返す
     *
     * @param array $players
     * @param array $favorite_players
     * @return array
     */
    private function makePlayerLists( array $players, array $favorite_player_ids ): array
    {
        $player_lists = array();

        foreach ( $players as $index => $player ) {

            $player_lists[$index]['id'] = $player['id'];
            $player_lists[$index]['name_jp'] = $player['name_jp'];
            $player_lists[$index]['name_en'] = $player['name_en'];
            $player_lists[$index]['country'] = $player['country'];
            $player_lists[$index]['wiki_url'] = $player['wiki_url'];
            $player_lists[$index]['age'] = $player['age'];
            $player_lists[$index]['favorite_status'] = 0;

            if ( count($favorite_player_ids) > 0 ) {
                for ($i=0; $i<count($favorite_player_ids); $i++) {
                    if ( $player_lists[$index]['id'] === $favorite_player_ids[$i] ) {
                        $player_lists[$index]['favorite_status'] = 1;
                    }
                }
            }
        }

        return $player_lists;
    }
}
