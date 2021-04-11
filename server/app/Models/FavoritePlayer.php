<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoritePlayer extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'favorite_players';
    protected $fillable = [
        'player_id',
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
            'player_id',
            'user_id',
        ];
    }

    /* ---------- リレーション定義 ---------- */

    public function players()
    {
        return $this->belongsTo(Player::class);
    }

    public function users()
    {
        return $this->belongsTo(Usesr::class);
    }
}
