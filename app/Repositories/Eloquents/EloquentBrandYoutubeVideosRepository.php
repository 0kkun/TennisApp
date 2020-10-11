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
     * 全レコード取得
     *
     * @return LengthAwarePaginator
     */
    public function getAll(): LengthAwarePaginator
    {
        return $this->brand_youtube_videos
                    ->orderBy('post_time', 'desc')
                    ->paginate(20);
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
     * @param array $brand_ids
     * @return LengthAwarePaginator
     */
    public function getVideosByBrandIds( array $brand_ids ): LengthAwarePaginator
    {
        return $this->brand_youtube_videos
                    ->whereIn('brand_id', $brand_ids)
                    ->orderBy('post_time', 'desc')
                    ->paginate(3, ["*"], 'brandyoutubepage'); // パラメータ名を指定することでページネーションを独立させる
    }
}