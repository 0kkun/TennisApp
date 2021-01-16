<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourSchedule extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'tour_schedules';
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
            'name',
            'location',
            'surface',
            'category',
            'year',
            'start_date',
            'end_date',
            'updated_at'
        ];
    }
}