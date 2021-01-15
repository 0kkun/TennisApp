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
     * 全レコードを取得
     * 
     * @param integer $num
     * @param bool $is_paginate
     * @return LengthAwarePaginator|Collection
     */ 
    public function getAll(int $num, bool $is_paginate)
    {
        return $this->youtube_videos
                    ->orderBy('created_at', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.MOVIE_LINK_NUM'), ["*"], 'youtubepage'); 
                    }, function ($query) use ($num) {
                        return $query->limit($num)->get();
                    });
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
     * @param integer $num
     * @param array $player_ids
     * @param bool $is_paginate
     * @return LengthAwarePaginator|Collection
     */
    public function getVideosByPlayerIds(int $num, array $player_ids, bool $is_paginate)
    {
        return $this->youtube_videos
                    ->whereIn('player_id', $player_ids)
                    ->limit($num)
                    ->orderBy('created_at', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.MOVIE_LINK_NUM'), ["*"], 'youtubepage'); 
                    }, function ($query) use ($num) {
                        return $query->limit($num)->get();
                    });
    }
}