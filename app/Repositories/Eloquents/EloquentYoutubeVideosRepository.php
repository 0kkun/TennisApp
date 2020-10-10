<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\YoutubeVideosRepository;
use App\Models\YoutubeVideo;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class EloquentYoutubeVideosRepository implements YoutubeVideosRepository
{
  protected $youtube_videos;


    /**
    * @param object $youtube_videos
    */
    public function __construct(
        YoutubeVideo $youtube_videos
    )
    {
        $this->youtube_videos = $youtube_videos;
    }


    /**
     * 全レコード取得
     *
     * @return void
     */
    public function getAll(): Collection
    {
        return $this->youtube_videos
                    ->get();
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|YoutubeVideo|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->youtube_videos->bulkInsertOrUpdate($data);
    }
}