<?php

namespace App\Repositories\Eloquents;

use App\Models\FavoriteBrand;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class EloquentFavoriteBrandsRepository implements FavoriteBrandsRepository
{
    protected $favorite_brands;


    /**
    * @param object $favorite_brands
    */
    public function __construct(
        FavoriteBrand $favorite_brands
    )
    {
        $this->favorite_brands = $favorite_brands;
    }


    /**
     * ログインユーザーの登録したお気に入り選手レコードを取得
     *
     * @return Collection
     */
    public function getAll(?int $user_id=null): Collection
    {
        $current_user_id = $user_id ? $user_id : '';
        if ( isset(Auth::user()->id) ) {
            $current_user_id = Auth::user()->id;
        }
        return $this->favorite_brands
                    ->where('user_id', '=', $current_user_id)
                    ->get();
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|FavoriteBrands|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->favorite_brands->bulkInsertOrUpdate($data);
    }


    /**
     * お気に入りブランド削除メソッド
     *
     * @param array $data
     * @return void
     */
    public function deleteRecord(array $data): void
    {
        if ( !isset($data['user_id']) ) {
            $data['user_id'] = Auth::user()->id;
        }

        $this->favorite_brands
            ->where('user_id', $data['user_id'])
            ->where('brand_id', $data['favorite_brand_id'])
            ->delete();
    }


    /**
     * ログインユーザーが持っているお気に入りブランドの名前・製造国を取得する
     *
     * @return Collection
     */
    public function getFavoriteBrandData(): Collection
    {
        $current_user_id = Auth::user()->id;

        return $this->favorite_brands
                    ->where('user_id', '=', $current_user_id)
                    ->with('brands')
                    ->join('brands', 'favorite_brands.brand_id', 'brands.id')
                    ->get();
    }
}