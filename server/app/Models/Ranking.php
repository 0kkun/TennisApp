<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'rankings';
    protected $guarded = array('id');

    public $timestamps = true;

    /**
     * バルクインサート時のカラム指定
     *
     * @return array
     */
    protected function getUpdateColumnsOnDuplicate(): array
    {
        return [
            'rank',
            'most_highest',
            'name_en',
            'name_jp',
            'age',
            'country',
            'point',
            'rank_change',
            'point_change',
            'current_tour_result_en',
            'current_tour_result_jp',
            'pre_tour_result_en',
            'pre_tour_result_jp',
            'next_point',
            'max_point',
            'ymd',
            'updated_at',
        ];
    }
}
