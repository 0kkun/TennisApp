<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\NewsArticlesRepository;
use App\Repositories\Contracts\BrandNewsArticlesRepository;
use Exception;
use Illuminate\Http\Request;
use App\Repositories\Contracts\FavoritePlayersRepository;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Contracts\PlayersNewsArticleRepository;

class NewsController extends Controller
{
    private $favorite_player_repository;
    private $players_news_article_repository;


    const MAX_ARTICLE_NUM = 30;


    /**
     * リポジトリをDI
     *
     * @param FavoritePlayersRepository $favorite_player_repository
     * @param PlayersNewsArticleRepository $players_news_article_repository
     */
    public function __construct(
        FavoritePlayersRepository $favorite_player_repository,
        PlayersNewsArticleRepository $players_news_article_repository
    )
    {
        $this->favorite_player_repository = $favorite_player_repository;
        $this->players_news_article_repository = $players_news_article_repository;
    }


    /**
     * 新しいデザインのニューストップ画面
     */
    public function top()
    {
        $user_id = Auth::user()->id;
        return view('news.top', compact('user_id'));
    }


    /**
     * [API] お気に入りに基づいたテニスニュース記事を取得する
     *
     * @param Request $request
     * @return Json|Exception
     */
    public function fetchNews(Request $request)
    {
        try {
            $user_id = $request->input('user_id');

            $is_paginate = false;
            
            $favorite_players = $this->favorite_player_repository
                ->getFavoritePlayers($user_id)
                ->toArray();

            // お気に入り選手が無い場合は全件取得にする
            if ( empty($favorite_players) ) {
                $response = $this->players_news_article_repository
                    ->fetchArticles(self::MAX_ARTICLE_NUM, $is_paginate);
                return request()->json(200, $response);
            }

            // ファーストネームだけにする
            $player_names = $this->getFirstName( $favorite_players );

            // お気に入り選手の名前で記事を検索し取得
            $response = $this->players_news_article_repository
                ->fetchArticlesByPlayerNames($player_names, self::MAX_ARTICLE_NUM, $is_paginate);

            return request()->json(200, $response);
        } catch ( Exception $e ) {
            return response()->json($e->getMessage(), 500);
        }
    }


    /**
     * ファーストネームだけにして返す
     *
     * @param array $players
     * @return array
     */
    private function getFirstName( array $players ):array
    {
        $names = array();
        $kanji_pattern = "/^[一-龠]+$/u";

        foreach ( $players as $index => $player) {
            $frequency_count = substr_count($player['name_jp'], '・');

            // 出身が日本かつ漢字なら、最初の2文字を抜き出す
            if ( preg_match( $kanji_pattern, $player['name_jp']) && $player['country'] === '日本' ) {
                $names[$index] = mb_substr($player['name_jp'], 0, 2);

            // "・"が名前に入っていない場合はそのままいれる
            } else if ( $frequency_count === 0 ) {
                $names[$index] = $player['name_jp'];

            // "・"がある場合は最後の文字列だけいれる
            } else {
                $divided_name = explode("・",$player['name_jp']);
                $names[$index] = $divided_name[$frequency_count];
            }
        }
        return $names;
    }
}
