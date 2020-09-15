<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use BulkInsertOrUpdateTrait;

    protected $table = 'players';
    protected $guarded = [];
    protected $fillable = [
        'name_jp',
        'name_en',
        'wiki_url',
        'country',
        'age'
    ];

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
            'wiki_url',
            'country',
            'age',
            'updated_at',
        ];
    }
}
