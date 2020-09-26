<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtpRanking extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'atp_rankings';
    protected $guarded = [];
    protected $fillable = [
        'rank',
        'name',
        'country',
        'point',
        'ymd',
    ];


    /**
     * バルクインサート時のカラム指定
     *
     * @return array
     */
    protected function getUpdateColumnsOnDuplicate(): array
    {
        return [
            'rank',
            'name',
            'country',
            'point',
            'ymd',
        ];
    }
}
