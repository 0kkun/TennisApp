<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandNewsArticle extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'brand_news_articles';
    protected $fillable = [
        'title',
        'url',
        'post_time',
        'brand_name'
    ];

    /**
     * バルクインサート時のカラム指定
     *
     * @return array
     */
    protected function getUpdateColumnsOnDuplicate(): array
    {
        return [
            'title',
            'url',
            'post_time',
            'brand_name',
            'updated_at',
        ];
    }
}
