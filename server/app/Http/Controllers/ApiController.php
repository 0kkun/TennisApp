<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\BrandsRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use Exception;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    private $brands_repository;
    private $favorite_brands_repository;

    /**
     * リポジトリをDI
     * 
     * @param BrandsRepository $brands_repository
     */
    public function __construct(
        BrandsRepository $brands_repository,
        FavoriteBrandsRepository $favorite_brands_repository
    )
    {
        $this->brands_repository = $brands_repository;
        $this->favorite_brands_repository = $favorite_brands_repository;
    }


    /**
     * ブランド一覧表示用メソッド
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBrandsData(Request $request)
    {
        try {
            $user_id = $request->input('user_id');

            $brands = $this->brands_repository->getAll()->toArray();

            $favorite_brand_ids = $this->favorite_brands_repository
                ->getAll($user_id)
                ->pluck('brand_id')
                ->toArray();

            $brand_lists = $this->makeBrandLists( $brands, $favorite_brand_ids, $user_id );
            return json_encode($brand_lists);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * お気に入りブランド登録メソッド
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addBrand( Request $request )
    {
        try {
            $data['user_id'] = $request->input('user_id');
            $data['brand_id'] = $request->input('favorite_brand_id');

            if ( !empty($data) ) {
                $this->favorite_brands_repository->bulkInsertOrUpdate($data);
            }
            $response = $this->getBrandsData($request);
            return request()->json(200, $response);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * お気に入りブランド削除メソッド
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteBrand(Request $request)
    {
        try {
            $data['user_id'] = $request->input('user_id');
            $data['favorite_brand_id'] = $request->input('favorite_brand_id');
    
            if ( !empty($data) ) {
                $this->favorite_brands_repository->deleteRecord($data);
            }
            $response = $this->getBrandsData($request);
            return request()->json(200, $response);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * お気に入り選手に登録されている場合はフラグを立てつつ、一覧表示用のデータを作成
     *
     * @param array $brands
     * @param array $favorite_brand_ids
     * @return array
     */
    private function makeBrandLists( array $brands, array $favorite_brand_ids, $user_id ): array
    {
        $brand_lists = array();

        foreach ( $brands as $index => $brand ) {

            $brand_lists[$index]['id']              = $brand['id'];
            $brand_lists[$index]['name_jp']         = $brand['name_jp'];
            $brand_lists[$index]['name_en']         = $brand['name_en'];
            $brand_lists[$index]['country']         = $brand['country'];
            $brand_lists[$index]['favorite_status'] = 0;
            $brand_lists[$index]['user_id']         = $user_id;

            if ( count($favorite_brand_ids) > 0 ) {
                for ( $i=0; $i<count($favorite_brand_ids); $i++ ) {
                    if ( $brand_lists[$index]['id'] === $favorite_brand_ids[$i] ) {
                        $brand_lists[$index]['favorite_status'] = 1;
                    }
                }
            }
        }

        $based_key = 'favorite_status';
        $brand_lists = $this->sortByKey( $brand_lists, $based_key );

        return $brand_lists;
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
