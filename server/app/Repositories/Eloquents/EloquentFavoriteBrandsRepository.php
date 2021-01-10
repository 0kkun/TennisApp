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
    public function getAll(): Collection
    {
        $current_user_id = null;
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
        $data['user_id'] = Auth::user()->id;
        $this->favorite_brands->bulkInsertOrUpdate($data);
    }


    /**
     * お気に入りブランド削除メソッド
     *
     * @param integer $favorite_player_id
     * @return void
     */
    public function deleteRecord(int $favorite_brand_id): void
    {
        $current_user_id = Auth::user()->id;

        $this->favorite_brands
            ->where('user_id', $current_user_id)
            ->where('brand_id', $favorite_brand_id)
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