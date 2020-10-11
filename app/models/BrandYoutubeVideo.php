<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandYoutubeVideo extends Model
{
  use BulkInsertOrUpdateTrait;

  protected $table = 'brand_youtube_videos';
  protected $guarded = [];
  protected $fillable = [
      'title',
      'url',
      'post_time',
      'brand_id',
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
          'brand_id',
          'updated_at',
      ];
  }

  /* ---------- リレーション定義 ---------- */

  public function players()
  {
      return $this->hasMany(Brand::class);
  }
}
