<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'youtube_videos';
    protected $fillable = [
        'title',
        'url',
        'post_time',
        'player_id',
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
            'player_id',
            'updated_at',
        ];
    }

    /* ---------- リレーション定義 ---------- */

    public function players()
    {
        return $this->hasMany(Player::class);
    }
}
