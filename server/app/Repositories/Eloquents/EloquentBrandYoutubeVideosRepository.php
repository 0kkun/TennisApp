<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\BrandYoutubeVideosRepository;
use App\Models\BrandYoutubeVideo;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentBrandYoutubeVideosRepository implements BrandYoutubeVideosRepository
{
    protected $brand_youtube_videos;


    /**
     * コンストラクタ
     *
     * @param BrandYoutubeVideo $brand_youtube_videos
     */
    public function __construct(
        BrandYoutubeVideo $brand_youtube_videos
    )
    {
        $this->brand_youtube_videos = $brand_youtube_videos;
    }


    /**
     * 全レコードを取得
     *
     * @param integer $num
     * @param boolean $is_paginate
     * @return Collection|LengthAwarePaginator
     */
    public function getAll(int $num, bool $is_paginate)
    {
        return $this->brand_youtube_videos
                    ->orderBy('created_at', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.MOVIE_LINK_NUM'), ["*"], 'brandyoutubepage'); 
                    }, function ($query) use ($num) {
                        return $query->limit($num)->get();
                    });
    }


    /**
     * バルクインサート処理
     *
     * @param  Collection|BrandYoutubeVideo|array $data
     * @return void
     */
    public function bulkInsertOrUpdate($data): void
    {
        $this->brand_youtube_videos->bulkInsertOrUpdate($data);
    }


    /**
     * brand_idを元にyoutube動画を取得する
     *
     * @param integer $num
     * @param array $brand_ids
     * @param boolean $is_paginate
     * @return void
     */
    public function getVideosByBrandIds(int $num, array $brand_ids, bool $is_paginate)
    {
        return $this->brand_youtube_videos
                    ->whereIn('brand_id', $brand_ids)
                    ->orderBy('created_at', 'desc')
                    ->when($is_paginate, function ($query) {
                        // パラメータ名を指定することでページネーションを独立させる
                        return $query->paginate(config('const.PAGINATE.MOVIE_LINK_NUM'), ["*"], 'brandyoutubepage'); 
                    }, function ($query) use ($num) {
                        return $query->limit($num)->get();
                    });
    }
}