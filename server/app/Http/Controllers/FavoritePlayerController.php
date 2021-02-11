<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\PlayersRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Services\Api\ApiServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FavoritePlayerController extends Controller
{
    private $players_repository;
    private $favorite_players_repository;
    private $api_service;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    /**
     * Constructor
     *
     * @param PlayersRepository $players_repository
     * @param FavoritePlayersRepository $favorite_players_repository
     * @param ApiServiceInterface $api_service
     */
    public function __construct(
        PlayersRepository $players_repository,
        FavoritePlayersRepository $favorite_players_repository,
        ApiServiceInterface $api_service
    )
    {
        $this->response = config('api_template.response_format');
        $this->result_status = config('api_template.result_status');
        $this->players_repository = $players_repository;
        $this->favorite_players_repository = $favorite_players_repository;
        $this->api_service = $api_service;
    }


    /**
     * 新しいデザインのお気に入りプレイヤー登録トップ画面
     */
    public function top()
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            return view('favorite_player.top',compact('user_id'));
        } else {
            return view('top.index');
        }
    }


    /**
     * [API] ユーザーがお気に入り選手登録済みかどうかのステータス付きで選手一覧を取得する
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchPlayers(Request $request): JsonResponse
    {
        try {
            $start = microtime(true);
            Log::info("[START] " . __FUNCTION__ );

            // リクエストの中身をチェック
            $is_varidation_error = $this->checkValidationError(__FUNCTION__, $request->all());
            $status = $this->getStatusCode($is_varidation_error);

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

            $end = microtime(true);
            $time = $this->api_service->calcTime($start, $end);
            Log::info("[ END ] " . __FUNCTION__ . ", STATUS:" . $status . ", 処理時間:" . $time . "秒");

        } catch (\Exception $e) {
            Log::info("[Exception]" . __FUNCTION__ . $e->getMessage());
            $this->respose = $this->api_service->makeErrorResponse($e);
        }

        return response()->json($this->response);
    }


    /**
     * [API] お気に入り選手登録
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addPlayer(Request $request): JsonResponse
    {
        try {
            $start = microtime(true);
            Log::info("[START] " . __FUNCTION__ );

            // リクエストの中身をチェック
            $is_varidation_error = $this->checkValidationError(__FUNCTION__, $request->all());
            $status = $this->getStatusCode($is_varidation_error);

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

            $end = microtime(true);
            $time = $this->api_service->calcTime($start, $end);
            Log::info("[ END ] " . __FUNCTION__ . ", STATUS:" . $status . ", 処理時間:" . $time . "秒");

        } catch (\Exception $e) {
            Log::info("[Exception]" . __FUNCTION__ . $e->getMessage());
            $this->respose = $this->api_service->makeErrorResponse($e);
        }

        return response()->json($this->response);
    }


    /**
     * [API] お気に入り選手削除
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deletePlayer(Request $request): JsonResponse
    {
        try {
            $start = microtime(true);
            Log::info("[START] " . __FUNCTION__ );

            // リクエストの中身をチェック
            $is_varidation_error = $this->checkValidationError(__FUNCTION__, $request->all());
            $status = $this->getStatusCode($is_varidation_error);

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

            $end = microtime(true);
            $time = $this->api_service->calcTime($start, $end);
            Log::info("[ END ] " . __FUNCTION__ . ", STATUS:" . $status . ", 処理時間:" . $time . "秒");

        } catch (\Exception $e) {
            Log::info("[Exception]" . __FUNCTION__ . $e->getMessage());
            $this->respose = $this->api_service->makeErrorResponse($e);
        }

        return response()->json($this->response);
    }


    /**
     * [API] インクリメンタルサーチメソッド
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchPlayers(Request $request): JsonResponse
    {
        try {
            $start = microtime(true);
            Log::info("[START] " . __FUNCTION__ );

            // リクエストの中身をチェック
            $is_varidation_error = $this->checkValidationError(__FUNCTION__, $request->all());
            $status = $this->getStatusCode($is_varidation_error);

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

            $end = microtime(true);
            $time = $this->api_service->calcTime($start, $end);
            Log::info("[ END ] " . __FUNCTION__ . ", STATUS:" . $status . ", 処理時間:" . $time . "秒");

        } catch (\Exception $e) {
            Log::info("[Exception]" . __FUNCTION__ . $e->getMessage());
            $this->respose = $this->api_service->makeErrorResponse($e);
        }

        return response()->json($this->response);
    }


    /**
     * バリデーションエラーか判定する
     *
     * @param string $func_name
     * @param array $check_keys
     * @return boolean
     */
    private function checkValidationError(string $func_name, array $check_keys): bool
    {
        $func_and_keys_pattern = [
            'fetchPlayers' => [
                'user_id' => 'required|integer'
            ],
            'addPlayer' => [
                'user_id'            => 'required|integer',
                'favorite_player_id' => 'required|integer',
            ],
            'deletePlayer' => [
                'user_id'            => 'required|integer',
                'favorite_player_id' => 'required|integer',
            ],
            'searchPlayers' => [
                'keyword' => 'required|string|between:1,100',
                'user_id' => 'required|integer',
            ],
        ];
        $validator = Validator::make($check_keys, $func_and_keys_pattern[$func_name]);

        $is_validation_error = !empty($validator->errors()->messages());

        return $is_validation_error;
    }


    /**
     * バリデーションチェックの結果に基づくステータスコードを取得
     *
     * @param boolean $is_validation_error
     * @return integer
     */
    private function getStatusCode(bool $is_validation_error): int
    {
        return $is_validation_error ? $this->result_status['bad_request'] : $this->result_status['success'];
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
    // public function index( Request $request )
    // {
    //     // 入力された検索パラメータ取得
    //     $inputs = $request->all();

    //     $params = [
    //         'name'    => $inputs['name'] ?? '',
    //         'country' => $inputs['country'] ?? '',
    //         'age'     => $inputs['age'] ?? '',
    //     ];

    //     $players = $this->favorite_player_service->searchPlayers($inputs);

    //     $favorite_player_ids = $this->favorite_players_repository->getAll()->pluck('player_id');

    //     $player_lists = $this->makePlayerLists( $players, $favorite_player_ids );

    //     $country_names = $this->players_repository->getAllCountryNames();

    //     return view('favorite_player.index', compact('player_lists','params','country_names'));
    // }


    /**
     * お気に入り選手登録メソッド
     *
     * @param Request $request
     * @return void
     */
    // public function add( Request $request )
    // {
    //     $data['player_id'] = $request->favorite_player_id;
    //     $data['user_id'] = Auth::user()->id;

    //     // バルクインサートで保存
    //     if ( !empty($data) ) {
    //         $this->favorite_players_repository->bulkInsertOrUpdate( $data );
    //     }

    //     session()->flash('flash_success', 'You added player!');

    //     return redirect()->route('favorite_player.index');
    // }


    /**
     * お気に入り選手削除メソッド
     *
     * @param Request $request
     * @return void
     */
    // public function remove(Request $request)
    // {
    //     $data['player_id'] = $request->favorite_player_id;
    //     $data['user_id'] = Auth::user()->id;

    //     $this->favorite_players_repository->deleteRecord($data);

    //     session()->flash('flash_alert', 'You removed player.');

    //     return redirect()->route('favorite_player.index');
    // }
}
