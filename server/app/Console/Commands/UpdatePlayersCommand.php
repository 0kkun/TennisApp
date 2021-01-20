<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\BatchLogger;
use Exception;
use Carbon\Carbon;
use App\Repositories\Contracts\RankingRepository;
use App\Repositories\Contracts\PlayersRepository;

class UpdatePlayersCommand extends Command
{
    protected $signature = 'command:updatePlayer';
    protected $description = 'ランキングテーブルからプレイヤーリストを生成する';

    private $ranking_repository;
    private $player_repository;

    const CHUNK_SIZE = 100;
    const RECORD_COUNT = 1000;

    // Youtube動画取得をONに設定
    const YOUTUBE_ACTIVE_PLAYERS = [
        'Novak Djokovic',
        'Rafael Nadal',
        'Dominic Thiem',
        'Daniil Medvedev',
        'Roger Federer',
        'Stefanos Tsitsipas',
        'Alexander Zverev',
        'Gael Monfils',
        'Fabio Fognini',
        'Stan Wawrinka',
        'Kei Nishikori',
        'Yoshihito Nishioka',
        'Tatsuma Ito',
        'Sho Shimabukuro',
        'Brandon Nakashima'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        RankingRepository $ranking_repository,
        PlayersRepository $player_repository
    )
    {
        parent::__construct();
        $this->ranking_repository = $ranking_repository;
        $this->player_repository = $player_repository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("【実行開始】");
        $this->logger = new BatchLogger( 'updatePlayer' );

        try {
            $insert_values = [];
            $youtube_active = null;

            $rankings = $this->ranking_repository->fetchRankings(self::RECORD_COUNT);
            $now = Carbon::now();
    
            foreach ($rankings as $rank) {

                if ( in_array($rank->name_en, self::YOUTUBE_ACTIVE_PLAYERS) )  $youtube_active = 1;
                else $youtube_active = null;
                $youtube_active = in_array($rank->name_en, self::YOUTUBE_ACTIVE_PLAYERS) ? 1 : 0;

                $insert_values[] = [
                    'name_jp'        => $rank->name_jp,
                    'name_en'        => $rank->name_en,
                    'country'        => $rank->country,
                    'age'            => $rank->age,
                    'updated_at'     => $now,
                    'created_at'     => $now,
                    'youtube_active' => $youtube_active
                ];
            }
    
            if ( !empty($insert_values) ) {
                $values = array_chunk($insert_values, self::CHUNK_SIZE);
                foreach ( $values as $value ) {
                    $this->player_repository->bulkInsertOrUpdate($value);
                }
                $this->logger->write(count($insert_values) . '件、テーブル保存or更新完了', 'info' ,true);
            }

            $this->logger->success();

        } catch (Exception $e) {
            $this->logger->exception($e);
        }
        unset($this->logger);

        $this->info("【実行終了】");
    }
}