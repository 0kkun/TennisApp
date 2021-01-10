<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourInformation extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'tour_informations';
    protected $guarded = [];
    protected $fillable = [
        'name',
        'category',
        'draw_num',
        'start_date',
        'end_date',
        'year',
        'surface',
        'location',
    ];


    /**
     * バルクインサート時のカラム指定
     *
     * @return array
     */
    protected function getUpdateColumnsOnDuplicate(): array
    {
        return [
            'name',
            'category',
            'draw_num',
            'start_date',
            'end_date',
            'year',
            'surface',
            'location',
            'updated_at',
        ];
    }
}
