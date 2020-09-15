<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Weidner\Goutte\GoutteFacade;
use App\Modules\BatchLogger;
use Exception;
use App\Repositories\Contracts\PlayersRepository;
use Carbon\Carbon;

class scrapePlayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scrapePlayers';

    private $players_repository;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[Player] Player nameをwikiからスクレイピングで取得するコマンド';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $this->logger = new BatchLogger('scrapePlayers');
        $origin_data = array(); // 全テキスト取得用

        try {
            $this->logger->write('取得開始', 'info', true);

            $goutte = GoutteFacade::request('GET', 'https://ja.wikipedia.org/wiki/男子テニス選手一覧');
            // アクセスしたら一旦sleep
            sleep(1);

            // 全てのテキスト取得
            $goutte->filter('ul li')->each(function ($node) use (&$origin_data) {
                // ノードが空の時のエラーハンドリング
                if ($node->count() > 0) {
                    // ここでは配列が返されるのでarray_pushが必要
                    array_push( $origin_data, $node->text() );
                } else {
                    $this->info("スクレイピング実行できませんでした。");
                }
            });

            $all_data = $this->excludeOtherInfo($origin_data);

            $count = count($all_data);

            // 日本語の名前だけを抽出
            for ($i=0; $i<$count; $i++){
                if ( preg_match('@^(?:)?([^ ]+)@i', $all_data[$i], $matches) ) {
                    $name_jp[$i] = $matches[0];
                } else {
                    $name_jp[$i] = null;
                }
            }

            // 英語の名前だけを抽出。"("または"（"から始まり、大文字小文字含めた英語で直前の0文字以上にマッチするもの。
            for ($i=0; $i<$count; $i++){
                if ( preg_match('/([^(|（])+([A-Za-z])+/u', $all_data[$i], $matches) ) {
                    $name_en[$i] = $matches[0];
                } else {
                    $name_en[$i] = null;
                }
            }

            // 出身国名を抽出。全角括弧で囲まれている文字を抽出する
            for ($i=0; $i<$count; $i++){
                if (preg_match('/(?<=[（]).*?(?=[）])/u', $all_data[$i], $matches)) {
                    $country[$i] = $matches[0];
                } else {
                    $country[$i] = null;
                }
            }

            // 年齢抽出。選手のリンク先で取得する
            for ( $i=0; $i<$count; $i++) {
                $wiki_url[$i] = 'https://ja.wikipedia.org//wiki/' . $name_jp[$i];
                $goutte_detail = GoutteFacade::request('GET', $wiki_url[$i]);
                sleep(0.5);

                if($goutte_detail->filter('.infobox')->count() > 0) {
                    $side_bar_text = $goutte_detail->filter('.infobox')->text();
                    // 文字列の中から2桁の数値であり、かつ"歳"の直前である物を抽出
                    $age[$i] = $this->extract_age($side_bar_text);
                    $this->info($i . ':' . $age[$i] . '歳');
                } else {
                    $age[$i] = null;
                }
            }

            $today = Carbon::now();

            for ($i=0; $i<$count; $i++) {
                $data[$i] = [
                    'name_jp'    => $name_jp[$i],
                    'name_en'    => $name_en[$i],
                    'country'    => $country[$i],
                    'wiki_url'   => $wiki_url[$i],
                    'age'        => $age[$i],
                    'created_at' => $today,
                    'updated_at' => $today,
                ];
            }

            $this->logger->write('取得完了', 'info' ,true);

            // バルクインサートで保存
            if (!empty($data)) {
                $this->players_repository->bulkInsertOrUpdate($data);
            }

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
     * @return Array
     */
    private function excludeOtherInfo( array $data ): Array
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
     * 年齢のみ抽出する
     *
     * @param string ソース文字列
     * @return 抽出した数値
     */
    function extract_age( string $subject )
    {

        $pattern1 = '/[[0-9]{2}歳/u';

        if (!preg_match($pattern1, $subject, $text)) {
            return null;
        }

        $pattern = '/[0-9.,０-９．，]+/u'; // パターン。末尾のuを忘れずに。

        if ( $result = preg_match( $pattern, $text[0], $matches ) ) {
            // マッチングした数字を抜き出す
            $num = $matches[0];
            // 半角数字に変換
            $num_half_width = mb_convert_kana( $num, 'anr' );
            // 区切りカンマを削除
            $num_plain = preg_replace( '/,/', '', $num_half_width );
            // 小数の値として正規化
            $num_int = (int) $num_plain;

            if ( $num_int <= 15 ) {
                $num_int = null;
            }

            return $num_int;
        } else {
            return null;
        }
    }
}
