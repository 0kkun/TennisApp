<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\BrandsRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Modules\BatchLogger;
use App\Services\Api\ApiServiceInterface;
use Illuminate\Http\JsonResponse;

class FavoriteBrandController extends Controller
{
    private $brands_repository;
    private $favorite_brands_repository;
    private $api_service;
    private $logger;

    // レスポンスのフォーマット
    protected $response;
    protected $result_status;

    /**
     * Constructor
     *
     * @param BrandsRepository $brands_repository
     * @param FavoriteBrandsRepository $favorite_brands_repository
     * @param ApiServiceInterface $api_service
     */
    public function __construct(
        BrandsRepository $brands_repository,
        FavoriteBrandsRepository $favorite_brands_repository,
        ApiServiceInterface $api_service
    )
    {
        $this->logger = new BatchLogger(__CLASS__);
        $this->response = config('api_template.response_format');
        $this->result_status = config('api_template.result_status');
        $this->brands_repository = $brands_repository;
        $this->favorite_brands_repository = $favorite_brands_repository;
        $this->api_service = $api_service;
    }


    /**
     * ブランド登録トップ画面
     */
    public function top()
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            return view('favorite_brand.top', compact('user_id'));
        } else {
            return view('top.index');
        }
    }


    /**
     * [API] ブランド一覧表示用メソッド
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchBrands(Request $request, ?bool $is_internal=false): JsonResponse
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ($status === $this->result_status['success']) {

                $user_id = $request->input('user_id');

                $brands = $this->brands_repository->getAll();

                $favorite_brand_ids = $this->favorite_brands_repository
                    ->getAll($user_id)
                    ->pluck('brand_id');

                $brand_lists = $this->makeBrandLists($brands, $favorite_brand_ids);
                $this->response = ['status' => $status, 'data' => $brand_lists];

            } else {
                $this->response = ['status' => $status,'data' => ''];
            }

            $this->logger->write('status code :' . $status, 'info');
            $this->logger->success();

            return response()->json($this->response);

        } catch (\Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * [API] お気に入りブランド登録メソッド
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addBrand(Request $request): JsonResponse
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id', 'favorite_brand_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ( $status === $this->result_status['success'] ) {
                $data['user_id'] = $request->input('user_id');
                $data['brand_id'] = $request->input('favorite_brand_id');

                // 保存処理
                if ( !empty($data) ) $this->favorite_brands_repository->bulkInsertOrUpdate($data);
                $status = $this->result_status['created'];
                $this->response = ['status' => $status, 'data' => ''];
            }

            return response()->json($this->response);

        } catch (\Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * [API] お気に入りブランド削除メソッド
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBrand(Request $request): JsonResponse
    {
        try {
            // リクエストの中身をチェック
            $expected_key = ['user_id', 'favorite_brand_id'];
            $status = $this->api_service->checkArgs($request, $expected_key);

            if ( $status === $this->result_status['success'] ) {
                $data['user_id'] = $request->input('user_id');
                $data['brand_id'] = $request->input('favorite_brand_id');

                if ( !empty($data) ) $this->favorite_brands_repository->deleteRecord($data);
                $status = $this->result_status['success'];
                $this->response = ['status' => $status, 'data' => ''];
            }

            return response()->json($this->response);

        } catch (\Exception $e) {
            $this->logger->exception($e);
            $status = $this->result_status['server_error'];
            $error_info = $this->api_service->makeErrorInfo($e);
            $this->response = ['status' => $status,'data' => $error_info];

            return response()->json($this->response);
        }
    }


    /**
     * お気に入りが含まれている場合はフラグを立てつつ配列に加工する
     *
     * @param Collection $brands
     * @param Collection $favorite_brand_ids
     * @return array
     */
    private function makeBrandLists(Collection $brands, Collection $favorite_brand_ids): array
    {
        $brand_lists = [];

        foreach ( $brands as $index => $brand ) {
            $brand_lists[] = [
                'id'              => $brand->id,
                'name_jp'         => $brand->name_jp,
                'name_en'         => $brand->name_en,
                'country'         => $brand->country,
                'is_favorited'    => false
            ];

            if ( count($favorite_brand_ids) > 0 ) { 
                $brand_lists[$index]['is_favorited'] = $this->isFavorite($favorite_brand_ids, $brand_lists[$index]['id']);
            }
        }

        $based_key = 'is_favorited';
        $brand_lists = $this->sortByKey($brand_lists, $based_key);

        return $brand_lists;
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
     * ブランドトップ画面
     */
    // public function index()
    // {
    //     $brands = $this->brands_repository->getAll();

    //     $favorite_brand_ids = $this->favorite_brands_repository->getAll()->pluck('brand_id');
        
    //     $brand_lists = $this->makeBrandLists($brands, $favorite_brand_ids);

    //     $user_id = Auth::user()->id;

    //     return view('favorite_brand.index',compact('brand_lists','user_id'));
    // }

    /**
     * お気に入りブランド登録メソッド
     */
    // public function add( Request $request )
    // {
    //     try {
    //         $data['brand_id'] = $request->favorite_brand_id;
    //         $data['user_id'] = Auth::user()->id;

    //         // バルクインサートで保存
    //         if ( !empty($data) ) {
    //             $this->favorite_brands_repository->bulkInsertOrUpdate($data);
    //         }
    //         session()->flash('flash_success', 'You added brand!');
    //         return redirect()->route('favorite_brand.top');

    //     } catch (Exception $e) {
    //         session()->flash('flash_danger', 'You have an error!');
    //         return redirect()->route('favorite_brand.top');
    //     }
    // }

    /**
     * お気に入りブランド削除メソッド
     */
    // public function remove( Request $request )
    // {
    //     try {
    //         $data['brand_id'] = $request->favorite_brand_id;
    //         $data['user_id'] = Auth::user()->id;

    //         if ( !empty($data) ) {
    //             $this->favorite_brands_repository->deleteRecord($data);
    //         }
    //         session()->flash('flash_alert', 'You removed brand.');
    //         return redirect()->route('favorite_brand.index');

    //     } catch (Exception $e) {
    //         session()->flash('flash_danger', 'You have an error!');
    //         return redirect()->route('favorite_brand.index');
    //     }
    // }
}
