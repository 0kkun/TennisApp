<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsArticle extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'news_articles';
    protected $guarded = [];
    protected $fillable = [
        'title',
        'url',
        'post_time'
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
          'updated_at',
        ];
    }
}