<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Contracts\BrandsRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class FavoriteBrandController extends Controller
{
    private $brands_repository;
    private $favorite_brands_repository;


    /**
     * リポジトリをDI
     *
     * @param BrandsRepository $brands_repository
     * @param FavoriteBrandsRepository $favorite_brands_repository
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
     * ブランド登録トップ画面
     *
     * @return void
     */
    public function top()
    {
        $user_id = Auth::user()->id;

        return view('favorite_brand.top', compact('user_id'));
    }


    /**
     * [API] ブランド一覧表示用メソッド
     *
     * @param Request $request
     * @return Json|Exception
     */
    public function fetchBrands(Request $request)
    {
        try {
            $user_id = $request->input('user_id');

            $brands = $this->brands_repository->getAll();

            $favorite_brand_ids = $this->favorite_brands_repository
                ->getAll($user_id)
                ->pluck('brand_id');

            $brand_lists = $this->makeBrandLists($brands, $favorite_brand_ids);
            return request()->json(200, $brand_lists);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * [API] お気に入りブランド登録メソッド
     *
     * @param Request $request
     * @return Json|Exception
     */
    public function addBrand( Request $request )
    {
        try {
            $data['user_id'] = $request->input('user_id');
            $data['brand_id'] = $request->input('favorite_brand_id');

            if ( !empty($data) ) {
                $this->favorite_brands_repository->bulkInsertOrUpdate($data);
            }
            $response = $this->fetchBrands($request);
            return request()->json(200, $response);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * [API] お気に入りブランド削除メソッド
     *
     * @param Request $request
     * @return Json|Exception
     */
    public function deleteBrand(Request $request)
    {
        try {
            $data['user_id'] = $request->input('user_id');
            $data['brand_id'] = $request->input('favorite_brand_id');
    
            if ( !empty($data) ) {
                $this->favorite_brands_repository->deleteRecord($data);
            }
            $response = $this->fetchBrands($request);
            return request()->json(200, $response);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
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
    public function index()
    {
        $brands = $this->brands_repository->getAll();

        $favorite_brand_ids = $this->favorite_brands_repository->getAll()->pluck('brand_id');
        
        $brand_lists = $this->makeBrandLists($brands, $favorite_brand_ids);

        $user_id = Auth::user()->id;

        return view('favorite_brand.index',compact('brand_lists','user_id'));
    }

    /**
     * お気に入りブランド登録メソッド
     */
    public function add( Request $request )
    {
        try {
            $data['brand_id'] = $request->favorite_brand_id;
            $data['user_id'] = Auth::user()->id;

            // バルクインサートで保存
            if ( !empty($data) ) {
                $this->favorite_brands_repository->bulkInsertOrUpdate($data);
            }
            session()->flash('flash_success', 'You added brand!');
            return redirect()->route('favorite_brand.top');

        } catch (Exception $e) {
            session()->flash('flash_danger', 'You have an error!');
            return redirect()->route('favorite_brand.top');
        }
    }

    /**
     * お気に入りブランド削除メソッド
     */
    public function remove( Request $request )
    {
        try {
            $data['brand_id'] = $request->favorite_brand_id;
            $data['user_id'] = Auth::user()->id;

            if ( !empty($data) ) {
                $this->favorite_brands_repository->deleteRecord($data);
            }
            session()->flash('flash_alert', 'You removed brand.');
            return redirect()->route('favorite_brand.index');

        } catch (Exception $e) {
            session()->flash('flash_danger', 'You have an error!');
            return redirect()->route('favorite_brand.index');
        }
    }
}
