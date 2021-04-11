<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayersNewsArticle extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'players_news_articles';
    protected $guarded = ['id'];

    public $timestamps = true;

    /**
     * バルクインサート時のカラム指定
     *
     * @return array
     */
    protected function getUpdateColumnsOnDuplicate(): array
    {
        return [
            'title',
            'image',
            'url',
            'post_time',
            'vender',
            'updated_at',
        ];
    }
}
