<?php

namespace Tests\Feature\Services;

use App\Repositories\Contracts\BrandNewsArticlesRepository;
use App\Repositories\Contracts\BrandYoutubeVideosRepository;
use App\Repositories\Contracts\FavoriteBrandsRepository;
use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Contracts\NewsArticlesRepository;
use App\Repositories\Contracts\YoutubeVideosRepository;
use App\Services\Top\TopService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use App\Models\User;
use App\Models\FavoritePlayer;
use App\Models\FavoriteBrand;
use App\Models\NewsArticle;
use Illuminate\Pagination\LengthAwarePaginator;

class TopServiceTest extends TestCase
{
    // use RefreshDatabase; // DBを使わずテストを作成しているので使用しない

    private $favorite_players_repository_mock;
    private $news_articles_repository_mock;
    private $brand_news_articles_repository_mock;
    private $favorite_brands_repository_mock;
    private $youtube_videos_repository_mock;
    private $brand_youtube_videos_repository_mock;

    private $top_service;

    public function setUp()
    {
        parent::setUp();

        // リポジトリをモック
        $this->setMockery();
        // インスタンスを指定
        $this->setMockInstance();

        // テストするサービスを指定
        $this->top_service = app(TopService::class);
    }


    public function tearDown()
    {
        parent::tearDown(); 
        Mockery::close();
    }

    private function setMockery()
    {
        $this->favorite_players_repository_mock = Mockery::mock(FavoritePlayersRepository::class);
        $this->news_articles_repository_mock = Mockery::mock(NewsArticlesRepository::class);
        // $this->brand_news_articles_repository_mock = Mockery::mock(BrandNewsArticlesRepository::class);
        // $this->favorite_brands_repository_mock = Mockery::mock(FavoriteBrandsRepository::class);
        // $this->youtube_videos_repository_mock = Mockery::mock(YoutubeVideosRepository::class);
        // $this->brand_youtube_videos_repository_mock = Mockery::mock(BrandYoutubeVideosRepository::class);
    }

    private function setMockInstance()
    {
        $this->app->instance(FavoritePlayersRepository::class, $this->favorite_players_repository_mock);
        $this->app->instance(NewsArticlesRepository::class, $this->news_articles_repository_mock);
        // $this->app->instance(BrandNewsArticlesRepository::class, $this->brand_news_articles_repository_mock);
        // $this->app->instance(FavoriteBrandsRepository::class, $this->favorite_brands_repository_mock);
        // $this->app->instance(YoutubeVideosRepository::class, $this->youtube_videos_repository_mock);
        // $this->app->instance(BrandYoutubeVideosRepository::class, $this->brand_youtube_videos_repository_mock);
    }


    /**
     * 正常系
     * @test
     */
    public function getArticleByFavoritePlayerのテスト()
    {
        $num = 5;
        $favorite_num = 2;
        $favorite_player_name[0] = '錦織　圭';

        $users = $this->setTestDataForUser($num);
        $players = $this->setTestDataForPlayer($num, null);
        $players_for_favorite = $this->setTestDataForPlayer($favorite_num, $favorite_player_name[0]);

        $user_id = $users->first()->id;
        $favorite_players = $this->setTestDataForFavoritePlayer($user_id, $players_for_favorite);

        $news_articles = $this->setTestDataForNewsArticle($num);
        $favorite_news_article = $this->setTestDataForFavoriteNewsArticle($favorite_player_name[0]);
        $favorite_news_article_pagenate = $this->convertPageNate($favorite_news_article, null, 1, 5, 1);

        // モックリポジトリのメソッドをセット
        $this->setFavotritePlayersRepositoryMethod('getAll', $favorite_players);
        $this->setFavotritePlayersRepositoryMethod('getFavoritePlayers', $players_for_favorite);
        $this->setNewsArticlesRepositoryMethod('getArticleByPlayerNames', $favorite_news_article_pagenate);

        $results = $this->top_service->getArticleByFavoritePlayer()->toArray();
        $result = $results['data'][0];
        
        $this->assertTrue($result['title'] === "錦織　圭のニュース");
    }


    /**
     * 正常系
     * @test
     */
    public function getArticleByFavoriteBrandメソッドのテスト()
    {
        $this->assertTrue(true);
    }




    // **************************** プライベートメソッド ****************************

    /**
     * ページネーションデータに変換する
     *
     * @param Collection $data
     * @param integer|null $size
     * @param integer $num_by_page
     * @param integer $page_num
     * @return LengthAwarePaginator
     */
    private function convertPageNate(Collection $data, ?int $size, int $num_by_page, int $page_num): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $data,          // 該当ページに表示するデータ
            $size,          // 配列の大きさ
            $num_by_page,   // 1ページ当たりの表示数
            $page_num       // 現在のページ番号
        );
    }

    /**
     * リポジトリメソッドをセット
     *
     * @param string $method
     * @param array $input
     * @param Collection $return
     * @return void
     */
    private function setFavotritePlayersRepositoryMethod(string $method, Collection $return): void
    {
        $this->favorite_players_repository_mock->shouldReceive($method)
            ->once()
            ->andReturn($return);
    }

    /**
     * リポジトリメソッドをセット
     *
     * @param string $method
     * @param LengthAwarePaginator $return
     * @return void
     */
    private function setNewsArticlesRepositoryMethod(string $method, LengthAwarePaginator $return): void
    {
        $this->news_articles_repository_mock
            ->shouldReceive($method)
            ->once()
            ->andReturn($return);
    }

    /**
     * プレイヤーデータ作成
     *
     * @param integer $num
     * @return Collection
     */
    private function setTestDataForUser(int $num): Collection
    {
        return factory(\App\Models\Player::class, $num)->make();
    }

    /**
     * ユーザーデータ作成
     *
     * @param integer $num
     * @param string|null $player_name
     * @return Collection
     */
    private function setTestDataForPlayer(int $num, ?string $player_name): Collection
    {
        if (empty($player_name)) return factory(\App\Models\Player::class, $num)->make();
        else return factory(\App\Models\Player::class, $num)->make([
            'name_jp' => $player_name
        ]);
    }

    /**
     * お気に入りプレイヤーデータ作成
     *
     * @param integer $user_id
     * @param Collection $players
     * @return Collection
     */
    private function setTestDataForFavoritePlayer(int $user_id, Collection $players): Collection
    {
        $favorite_players = collect();
        $count = count($players);
        for ( $i=0; $i<$count; $i++) {
            $favorite_player = factory(\App\Models\FavoritePlayer::class, 1)->make([
                'user_id'   => $user_id,
                'player_id' => $players[$i]['id'],
            ]);
            $favorite_players = $favorite_players->concat($favorite_player);
        }
        return $favorite_players;
    }

    /**
     * お気に入りブランドデータ作成
     *
     * @param integer $user_id
     * @param Collection $brands
     * @return Collection
     */
    private function setTestDataForFavoriteBrand(int $user_id, Collection $brands): Collection
    {
        $favorite_brands = collect();
        $count = count($brands);

        for ( $i=0; $i<$count; $i++) {
            $favorite_brand = factory(\App\Models\FavoriteBrand::class, 1)->make([
                'user_id'   => $user_id,
                'player_id' => $brands[$i]['id'],
            ]);
            $favorite_brands = $favorite_brands->concat($favorite_brand);
        }
        return $favorite_brands;
    }

    /**
     * ニュースデータ作成
     *
     * @param integer $num
     * @return Collection
     */
    private function setTestDataForNewsArticle(int $num): Collection
    {
        return factory(\App\Models\NewsArticle::class, $num)->make();
    }

    /**
     * お気に入り選手の名前がタイトルに含まれたニュースデータを作成
     *
     * @param string $player_name
     * @return Collection
     */
    private function setTestDataForFavoriteNewsArticle(string $player_name): Collection
    {
        return factory(\App\Models\NewsArticle::class, 1)->make([
            'title' => $player_name . 'のニュース'
        ]);
    }
}
