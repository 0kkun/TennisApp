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
            'most_heighest',
            'name',
            'age',
            'country',
            'point',
            'rank_change',
            'point_change',
            'current_tour_result',
            'pre_tour_result',
            'next_point',
            'max_point',
            'updated_at',
        ];
    }
}
