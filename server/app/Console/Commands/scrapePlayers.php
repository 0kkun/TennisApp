<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use App\Repositories\Contracts\PlayersRepository;
use Carbon\Carbon;

/**
 * MEMO: 現在は使用していない
 * 
 * [2020-09-17 02:52:18] local.INFO: [START] scrapePlayers  
 * [2020-09-17 02:52:18] local.INFO: [scrapePlayers] 取得開始 : 0.074466943740845秒  
 * [2020-09-17 02:53:44] local.INFO: [scrapePlayers] 取得完了 : 86.893354177475秒  
 * [2020-09-17 02:53:45] local.INFO: [scrapePlayers] 保存完了 : 87.135699987411秒  
 * [2020-09-17 02:53:45] local.INFO: [ END ] scrapePlayers 処理時間: 87.136131048203秒 
 * TODO: めっちゃ時間かかってるからJob化すべき。
 */
class scrapePlayers extends Command
{
    protected $signature = 'command:scrapePlayers';
    protected $description = '選手の情報をwikiからスクレイピングで取得し保存するコマンド';
    private $players_repository;


    /**
     * リポジトリのコンストラクタ
     *
     * @param PlayersRepository $players_repository
     */
    public function __construct(
        PlayersRepository $players_repository
    )
    {
        parent::__construct();
        $this->players_repository = $players_repository;
    }


    /**
     * wikiのテニス選手一覧から情報をスクレイピングする
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("実行開始");
        $this->logger = new BatchLogger( 'scrapePlayers' );

        try {
            $this->logger->write( '取得開始', 'info', true );

            $goutte = GoutteFacade::request( 'GET', 'https://ja.wikipedia.org/wiki/男子テニス選手一覧' );
            // アクセスしたら一旦sleep
            sleep(1);

            // 全てのテキスト取得
            $origin_data = $this->scrapePlayersAllText( $goutte );

            // 不必要なテキストを削除
            $all_data = $this->excludeOtherInfo( $origin_data );

             // 日本語の名前だけを抽出
            $player_data['name_jp'] = $this->abstractPlayersNameJp( $all_data );

            // 英語の名前だけを抽出
            $player_data['name_en'] = $this->abstractPlayersNameEn( $all_data );

            // 出身国名を抽出
            $player_data['country'] = $this->abstractPlayersCountry( $all_data );

            // 日本語名を使ってurlを作成し、選手の年齢をスクレイピング
            $detail_data = $this->scrapePlayersAgeAndUrl( $player_data['name_jp'] );
            $player_data['age'] = $detail_data['age'];
            $player_data['wiki_url'] = $detail_data['wiki_url'];

            // 保存用に加工。日付情報も付加。
            $value = $this->makeInsertValue($player_data);

            $this->logger->write('取得完了', 'info' ,true);
            $this->info("取得完了");

            // バルクインサートで保存
            if (!empty($value)) {
                $this->players_repository->bulkInsertOrUpdate($value);
            }

            $this->info("保存完了");
            $this->logger->write('保存完了', 'info' ,true);
            $this->logger->success();

        } catch (Exception $e) {
            $this->logger->exception($e);
        }
        unset($this->logger);

        $this->info("実行終了");
    }


    /**
     * 特定の不必要なワードを除外する
     *
     * @param array $data
     * @return array
     */
    private function excludeOtherInfo( array $data ): array
    {
        $exclude_words = array(
            "","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
            "女子テニス選手一覧","ATPツアー公式サイト","男子テニス選手","男子スポーツ選手一覧","テニス関連一覧","トーク","投稿記録","アカウント作成","ログイン",
            "ページ","ノート","閲覧","編集","履歴表示","メインページ","コミュニティ・ポータル","最近の出来事","新しいページ","最近の更新",
            "おまかせ表示","練習用ページ","アップロード (ウィキメディア・コモンズ)","ヘルプ","井戸端","お知らせ","バグの報告","寄付","ウィキペディアに関するお問い合わせ",
            "リンク元","関連ページの更新状況","ファイルをアップロード","特別ページ","この版への固定リンク","ページ情報","このページを引用","ウィキデータ項目","ブックの新規作成",
            "PDF 形式でダウンロード","印刷用バージョン","ウィキデータ","English","Eesti","Bahasa Indonesia","Lëtzebuergesch","Nederlands","Slovenčina",
            "Српски / srpski","Svenska","Türkçe","個人設定","クリエイティブ・コモンズ 表示-継承ライセンス","プライバシー・ポリシー","ウィキペディアについて","免責事項",
            "モバイルビュー","開発者","統計","Cookieに関する声明","ATPツアー公式サイト （英語）","ログインしていません",
            "テキストはクリエイティブ・コモンズ 表示-継承ライセンスの下で利用可能です。追加の条件が適用される場合があります。詳細は利用規約を参照してください。"
        );

        // 削除実行
        $result = array_diff($data, $exclude_words);
        // indexを詰める
        $result = array_values($result);
        // 最後の1つは時刻が入っている為、強制的に1つ削除する
        array_pop($result);

        return $result;
    }


    /**
     * 全てのテキストをスクレイピングで取得
     *
     * @param mixed $goutte
     * @return array
     */
    private function scrapePlayersAllText( $goutte ): array
    {
        $origin_data = array();

        // 全てのテキスト取得。&をつけないと直接渡しできない。
        $goutte->filter('ul li')->each(function ($node) use (&$origin_data) {
            // ノードが空の時のエラーハンドリング
            if ( $node->count() > 0 ) {
                // ここでは配列が返されるのでarray_pushが必要
                array_push( $origin_data, $node->text() );
            } else {
                $this->info("スクレイピング実行できませんでした。");
            }
        });

        return $origin_data;
    }


    /**
     * 正規表現で選手の日本語名だけを抽出
     * TODO: 正規表現のリファクタやる
     *
     * @param array $all_data
     * @return array
     */
    private function abstractPlayersNameJp(array $all_data): array
    {
        $count = count( $all_data );
        $pattern = '@^(?:)?([^ ]+)@i';

        for ( $i=0; $i < $count; $i++ ){
            if ( preg_match( $pattern, $all_data[$i], $matches )) {
                $name_jp[$i] = $matches[0];
            } else {
                $name_jp[$i] = null;
            }
        }

        return $name_jp;
    }


    /**
     * 正規表現で選手の英語名だけを抽出
     * TODO: 正規表現のリファクタやる
     *
     * @param array $all_data
     * @return array
     */
    private function abstractPlayersNameEn( array $all_data ): array
    {
        $count = count( $all_data );
        $pattern = '/([^(|（])+([A-Za-z])+/u';

        // 英語の名前だけを抽出。"("または"（"から始まり、大文字小文字含めた英語で直前の0文字以上にマッチするもの。
        for ( $i=0; $i < $count; $i++ ){
            if ( preg_match( $pattern, $all_data[$i], $matches )) {
                // 外国文字を変換しつつ格納
                $name_en[$i] = $this->transliterateString($matches[0]);
            } else {
                $name_en[$i] = null;
            }

        }

        return $name_en;
    }


    /**
     * 正規表現で選手の出身国だけを抽出
     * TODO: 正規表現のリファクタやる
     *
     * @param array $all_data
     * @return array
     */
    private function abstractPlayersCountry( array $all_data ): array
    {
        $count = count( $all_data );
        $pattern = '/(?<=[（]).*?(?=[）])/u';

        // 出身国名を抽出。全角括弧で囲まれている文字を抽出する
        for ( $i=0; $i < $count; $i++ ){
            if ( preg_match( $pattern, $all_data[$i], $matches )) {
                $country[$i] = $matches[0];
            } else {
                $country[$i] = null;
            }
        }

        return $country;
    }


    /**
     * 選手の名前を使ってurlを作成し、年齢をスクレイピング
     *
     * @param array $name_jp
     * @return array
     */
    private function scrapePlayersAgeAndUrl( array $name_jp ): array
    {
        $count = count( $name_jp );

        for ( $i=0; $i<$count; $i++) {
            $wiki_url[$i] = 'https://ja.wikipedia.org//wiki/' . $name_jp[$i];
            $goutte_detail = GoutteFacade::request('GET', $wiki_url[$i]);
            sleep(0.5);

            if( $goutte_detail->filter('.infobox')->count() > 0 ) {
                // サイドボックスのテキストを取得
                $side_bar_text = $goutte_detail->filter('.infobox')->text();

                // 文字列の中から2桁の数値であり、かつ"歳"の直前である物を抽出
                $age[$i] = $this->extractAge( $side_bar_text );

                $this->info( $i . '件目の年齢スクレイピング ' . $name_jp[$i] . ' : ' . $age[$i] . '歳' );
            } else {
                $age[$i] = null;
            }
        }

        return [
            'age' => $age,
            'wiki_url' => $wiki_url
        ];
    }


    /**
     * 年齢のみ抽出する
     * TODO: 正規表現のリファクタやる
     *
     * @param string $subject (ソース文字列)
     * @return 抽出した数値 or null
     */
    function extractAge( string $subject )
    {

        $pattern1 = '/[[0-9]{2}歳/u';

        if ( !preg_match( $pattern1, $subject, $text ) ) {
            return null;
        }

        $pattern = '/[0-9.,０-９．，]+/u';

        if ( preg_match( $pattern, $text[0], $matches ) ) {
            // マッチングした数字を抜き出す
            $num = $matches[0];
            // 半角数字に変換
            $num_half_width = mb_convert_kana( $num, 'anr' );
            // 区切りカンマを削除
            $num_plain = preg_replace( '/,/', '', $num_half_width );
            // 小数の値として正規化
            $num_int = (int) $num_plain;

            // 15歳以下は存在しないはずだが一応除く
            if ( $num_int <= 15 ) {
                $num_int = null;
            }

            return $num_int;
        } else {
            return null;
        }
    }


    /**
     * レコード保存用のデータを作成。
     * 日付情報を付加
     *
     * @param array $player_data
     * @return array
     */
    private function makeInsertValue( array $player_data ): array
    {
        $count = count( $player_data['name_jp'] );
        $today = Carbon::now();

        for ( $i=0; $i < $count; $i++ ) {
            $value[$i] = [
                'name_jp'    => $player_data['name_jp'][$i],
                'name_en'    => $player_data['name_en'][$i],
                'country'    => $player_data['country'][$i],
                'age'        => $player_data['age'][$i],
                'wiki_url'   => $player_data['wiki_url'][$i],
                'created_at' => $today,
                'updated_at' => $today
            ];
        }
        return $value;
    }

    /**
     * 外国の文字をローマの同等のものに変換する
     * キリル文字も変換する
     *
     * @param string $txt
     * @return string
     */
    private function transliterateString(string $txt): string
    {
        $transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
        return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
    }
}
