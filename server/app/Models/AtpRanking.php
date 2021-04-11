<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtpRanking extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'atp_rankings';
    protected $fillable = [
        'rank',
        'name',
        'country',
        'point',
        'ymd',
        'pre_rank',
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
            'pre_rank',
        ];
    }
}
