<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'brands';
    protected $guarded = [];
    protected $fillable = [
        'name_jp',
        'name_en',
        'country',
    ];

    /**
     * バルクインサート時のカラム指定
     *
     * @return array
     */
    protected function getUpdateColumnsOnDuplicate(): array
    {
        return [
            'name_jp',
            'name_en',
            'country',
        ];
    }

    /* ---------- リレーション定義 ---------- */

    public function favoriteBrand()
    {
        return $this->hasMany(FavoriteBrand::class);
    }
}
