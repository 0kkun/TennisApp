<?php

namespace App\Repositories\Eloquents;

use App\Models\Brand;
use App\Repositories\Contracts\BrandsRepository;
use Illuminate\Support\Collection;

class EloquentBrandsRepository implements BrandsRepository
{
    protected $brands;


    /**
    * @param object $brands
    */
    public function __construct(
        brand $brands
    )
    {
        $this->brands = $brands;
    }


    /**
     * 全レコード取得
     *
     * @return void
     */
    public function getAll(): Collection
    {
        return $this->brands
                    ->get();
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|Brands|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->brands->bulkInsertOrUpdate($data);
    }
}