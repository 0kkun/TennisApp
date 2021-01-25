<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {model_name : モデル名}';
    protected $description = 'リポジトリファイルを作成する';


    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model_name = $this->argument('model_name');

        // モデル名がちゃんと入力されているか
        if ($model_name === '' || is_null($model_name) || empty($model_name)) {
            $this->error('Model name invalid..!');
        } else {
            $contract_file_name = 'app/Repositories/Contracts/' . $model_name . 'Repository.php';
            $eloquent_file_name = 'app/Repositories/Eloquents/' . 'Eloquent' . $model_name . 'Repository.php';
        }

        // リポジトリ類のディレクトリが作成済みの場合
        if ( !file_exists('app/Repositories/Contracts') && !file_exists('app/Repositories/Eloquents' )) {
            mkdir('app/Repositories/Contracts', 0775, true);
            mkdir('app/Repositories/Eloquents', 0775, true);
            $this->createFiles($model_name, $contract_file_name, $eloquent_file_name);
        } else {
            $this->createFiles($model_name, $contract_file_name, $eloquent_file_name);
        }
    }


    /**
     * リポジトリファイルを作成するメソッド
     *
     * @param string $model_name
     * @param string $contract_file_name
     * @param string $eloquent_file_name
     * @return void
     */
    private function createFiles( string $model_name, string $contract_file_name, string $eloquent_file_name )
    {
        if( !file_exists($contract_file_name) && !file_exists($eloquent_file_name) ) {
            $contract_file_content = "<?php\n\nnamespace App\\Repositories\\Contracts;\n\ninterface " . $model_name . "Repository\n{\n}";

            file_put_contents($contract_file_name, $contract_file_content);

            $eloquent_file_content = "<?php\n\nnamespace App\\Repositories\\Eloquents;\n\nuse App\\Repositories\\Contracts\\".$model_name."Repository;\n\nclass " . "Eloquent" . $model_name . "Repository implements " . $model_name . "Repository\n{\n}";

            file_put_contents($eloquent_file_name, $eloquent_file_content);

            $this->info('Repository files created successfully.');
        } else {
            $this->error('Repository files already exists.');
        }
    }
}
