<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Services\FavoritePlayer\FavoritePlayerServiceInterface;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Collection;
use App\Services\Api\ApiServiceInterface;
use App\Modules\BatchLogger;

class FavoritePlayerController extends Controller
{
    private $players_repository;
    private $favorite_players_repository;
    private $favorite_player_service;
    private $api_service;
    private $logger;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    /**
     * リポジトリをDI
     *
     * @param PlayersRepository $players_repository
     * @param FavoritePlayersRepository $favorite_players_repository
     * @param FavoritePlayerServiceInterface $favorite_player_service
     */
    public function __construct(
        PlayersRepository $players_repository,
        FavoritePlayersRepository $favorite_players_repository,
        FavoritePlayerServiceInterface $favorite_player_service,
        ApiServiceInterface $api_service
    )
    {
        $this->logger = new BatchLogger('FavoritePlayerController');
        $this->response = config('api_template.response_format');
        $this->result_status = config('api_template.result_status');
        $this->players_repository = $players_repository;
        $this->favorite_players_repository = $favorite_players_repository;
        $this->favorite_player_service = $favorite_player_service;
        $this->api_service = $api_service;
    }


    /**
     * 新しいデザインのお気に入りプレイヤー登録トップ画面
     */
    public function top()
    {
        if (Auth::check()) {
            $user_id = Auth::user()->id;
            return view('favorite_player.top',compact('user_id'));
        } else {
            return view('top.index');
        }
    }


    /**
     * [API] ユーザーがお気に入り選手登録済みかどうかのステータス付きで選手一覧を取得する
     *
     * @param Request $request
     * @return Json
     */
    public function fetchPlayers(Request $request)
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $user_id = $request->input('user_id');

                $players = $this->players_repository
                    ->getAll();

                $favorite_player_ids = $this->favorite_players_repository
                    ->getAll($user_id)
                    ->pluck('player_id');

                $player_lists = $this->makePlayerLists($players, $favorite_player_ids);
                $this->response = ['status' => $status, 'data' => $player_lists];

            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            $this->logger->write('status code :' . $status, 'info');
            $this->logger->success();

            return response()->json($this->response);

        } catch (Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * [API] お気に入り選手登録
     *
     * @param Request $request
     * @return Json
     */
    public function addPlayer(Request $request)
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id', 'favorite_player_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $data['user_id'] = $request->input('user_id');
                $data['player_id'] = $request->input('favorite_player_id');

                if ( !empty($data) ) {
                    $this->favorite_players_repository->bulkInsertOrUpdate($data);
                    $status = $this->result_status['created'];
                }
                $this->response = ['status' => $status, 'data' => ''];

            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            return response()->json($this->response);

        } catch (Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * [API] お気に入り選手削除
     *
     * @param Request $request
     * @return Json
     */
    public function deletePlayer(Request $request)
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id', 'favorite_player_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {
                $data['user_id'] = $request->input('user_id');
                $data['player_id'] = $request->input('favorite_player_id');
        
                if ( !empty($data) ) {
                    $this->favorite_players_repository->deleteRecord($data);
                    $status = $this->result_status['deleted'];
                }
                $this->response = ['status' => $status, 'data' => ''];

            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            return response()->json($this->response);

        } catch (Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * [API] インクリメンタルサーチメソッド
     *
     * @param Request $request
     * @return Json
     */
    public function searchPlayers(Request $request)
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['keyword', 'user_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $inputs['name'] = $request->input('keyword');
                $user_id = $request->input('user_id');

                $players = $this->players_repository->searchPlayersByName($inputs);

                $favorite_player_ids = $this->favorite_players_repository
                    ->getAll($user_id)
                    ->pluck('player_id');

                $player_lists = $this->makePlayerLists($players, $favorite_player_ids);
                $this->response = ['status' => $status, 'data' => $player_lists];

            } else {
                $this->response = ['status' => $status, 'data' => ''];
            }

            return response()->json($this->response);

        } catch (Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * お気に入り選手が含まれている場合はフラグを立てつつ配列に加工する
     *
     * @param Collection $players
     * @param Collection $favorite_player_ids
     * @return array
     */
    private function makePlayerLists( Collection $players, Collection $favorite_player_ids ): array
    {
        $player_lists = [];

        foreach ( $players as $index => $player ) {
            $player_lists[] = [
                'id'              => $player->id,
                'name_jp'         => $player->name_jp,
                'name_en'         => $player->name_en,
                'country'         => $player->country,
                'age'             => $player->age,
                'is_favorited'    => false
            ];

            if ( count($favorite_player_ids) > 0 ) { 
                $player_lists[$index]['is_favorited'] = $this->isFavorite($favorite_player_ids, $player_lists[$index]['id']);
            }
        }

        $based_key = 'is_favorited';
        $player_lists = $this->sortByKey($player_lists, $based_key);

        return $player_lists;
    }


    /**
     * お気に入りかどうか判定する
     *
     * @param Collection $favorite_ids
     * @param integer $id
     * @return boolean
     */
    private function isFavorite(Collection $favorite_ids, int $id): bool
    {
        $is_favorited = false;
        foreach ( $favorite_ids as $favorite_id ) {
            if ( $favorite_id == $id ) $is_favorited = true;
        }
        return $is_favorited;
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

    /* --------------------- 以下、旧タイプのソース ---------------------- */

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

        $players = $this->favorite_player_service->searchPlayers($inputs);

        $favorite_player_ids = $this->favorite_players_repository->getAll()->pluck('player_id');

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
        $data['user_id'] = Auth::user()->id;

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
    public function remove(Request $request)
    {
        $data['player_id'] = $request->favorite_player_id;
        $data['user_id'] = Auth::user()->id;

        $this->favorite_players_repository->deleteRecord($data);

        session()->flash('flash_alert', 'You removed player.');

        return redirect()->route('favorite_player.index');
    }
}
