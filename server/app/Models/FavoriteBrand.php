<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteBrand extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'favorite_brands';
    protected $guarded = [];
    protected $fillable = [
        'brand_id',
        'user_id',
    ];

    /**
     * バルクインサート時のカラム指定
     *
     * @return array
     */
    protected function getUpdateColumnsOnDuplicate(): array
    {
        return [
            'brand_id',
            'user_id',
        ];
    }

    /* ---------- リレーション定義 ---------- */

    public function brands()
    {
        return $this->belongsTo(Brand::class);
    }

    public function users()
    {
        return $this->belongsTo(Usesr::class);
    }
}
