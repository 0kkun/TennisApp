<?php

namespace App\Models;

use App\Services\QueryBuilder\BulkInsertBuilder;
use Illuminate\Support\Collection;

trait BulkInsertOrUpdateTrait
{
    /**
     * バルクインサートビルダーのインスタンスを保持する
     * 1回目のインスタンス生成時にこのプロパティに格納される
     *
     * @var BulkInsertBuilder|null
     */
    private $bulk_insert_builder = null;

    /**
     * key制約が発生したときの、updateするカラムのデフォルト指定
     *
     * @return array
     *
     * ex: return ['foo', 'bar', 'updated_at'];
     */
    abstract protected function getUpdateColumnsOnDuplicate(): array;

    /**
     * レコードをバルクインサートする
     * もし重複キー制約が発生した場合、指定したカラムの値のみアップデートする
     *
     * @param array|Collection $values
     * @param array|null       $update_columns
     * @return void
     */
    public function bulkInsertOrUpdate($values, ?array $update_columns = null): void
    {
        // インスタンス生成. すでにあれば使い回す
        $this->bulk_insert_builder = $this->bulk_insert_builder ?? BulkInsertBuilder::build($this->query()->getQuery());

        // もし配列、モデル、コレクション以外のvaluesが渡されていたら、例外を投げて終了させる
        $this->bulk_insert_builder->validateValues($values);

        // カラムが明示的に渡された場合はそのカラムでupdateする
        $update_columns = $update_columns ?? $this->getUpdateColumnsOnDuplicate();

        $this->bulk_insert_builder->execute($values, $update_columns);
    }
}
