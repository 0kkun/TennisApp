<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'players';
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
            'name_jp',
            'name_en',
            'country',
            'age',
            'updated_at',
            'youtube_active'
        ];
    }

    /* ---------- リレーション定義 ---------- */

    public function favoritePlayer()
    {
        return $this->hasMany(FavoritePlayer::class);
    }
}
