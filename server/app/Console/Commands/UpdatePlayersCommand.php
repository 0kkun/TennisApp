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
        $this->logger = new BatchLogger( 'scrapePlayer' );

        try {
            $insert_values = [];

            $rankings = $this->ranking_repository->fetchRankings(self::RECORD_COUNT);
            $now = Carbon::now();
    
            foreach ($rankings as $rank) {
                $insert_values[] = [
                    'name_jp'    => $rank->name_jp,
                    'name_en'    => $rank->name_en,
                    'country'    => $rank->country,
                    'age'        => $rank->age,
                    'updated_at' => $now,
                    'created_at' => $now
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