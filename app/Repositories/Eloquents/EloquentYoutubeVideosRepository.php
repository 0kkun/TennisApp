<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\YoutubeVideosRepository;
use App\Models\YoutubeVideo;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @return LengthAwarePaginator
     */
    public function getAll(): LengthAwarePaginator
    {
        return $this->youtube_videos
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
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


    /**
     * player_idを元にyoutube動画を取得する
     *
     * @param array $player_ids
     * @return LengthAwarePaginator
     */
    public function getVideosByPlayerIds( array $player_ids ): LengthAwarePaginator
    {
        return $this->youtube_videos
                    ->whereIn('player_id', $player_ids)
                    ->orderBy('created_at', 'desc')
                    ->paginate(3, ["*"], 'youtubepage'); // パラメータ名を指定することでページネーションを独立させる
    }
}