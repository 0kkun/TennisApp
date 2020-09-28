<?php

namespace App\Services\Top;

use App\Repositories\Contracts\FavoritePlayersRepository;
use App\Repositories\Contracts\NewsArticlesRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\PlayersRepository;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TopService implements TopServiceInterface
{
    private $players_repository;
    private $favorite_players_repository;
    private $news_articles_repository;

    /**
     * TopController constructor.
     * @param PlayersRepository $players_repository
     */
    public function __construct(
        PlayersRepository $players_repository,
        FavoritePlayersRepository $favorite_players_repository,
        NewsArticlesRepository $news_articles_repository
    )
    {
        $this->players_repository = $players_repository;
        $this->favorite_players_repository = $favorite_players_repository;
        $this->news_articles_repository = $news_articles_repository;
    }


    /**
     * まずfavorite playerテーブルからログインユーザーのfavorite pyayer idを取得し、
     * playerテーブルからidに紐づくnameを取得する。
     * nameのfirst nameだけに加工した配列を作成し、
     * news articlesテーブルからwhereInで検索し、データを取得。TopControllerで使用する
     *
     * @return 
     */
    public function getArticleByFavoritePlayer()
    {
        if ( $this->hasFavorite() ) {
          // お気に入り選手の名前を取得
          $favorite_player_data = $this->favorite_players_repository->getFavoritePlayerData()->toArray();

          // ファーストネームだけにする
          $player_names = $this->getFirstName( $favorite_player_data );

          // ファーストネームを使って記事を取得
          $news_articles = $this->news_articles_repository->getArticleByPlayerNames( $player_names );

        } else {
          $news_articles = $this->news_articles_repository->getAll();
        }
        return $news_articles;
    }


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


    /**
     * ユーザーがお気に入り選手を登録しているかどうかチェック
     *
     * @return boolean
     */
    private function hasFavorite(): bool
    {
        $count = count($this->favorite_players_repository->getAll());

        if ( $count > 0 ) {
          return true;
        } else {
          return false;
        }
    }

}