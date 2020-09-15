<?php

namespace App\Services\QueryBuilder;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BulkInsertBuilder extends Builder
{
    const DUPLICATE = ' ON DUPLICATE KEY UPDATE ';

    const UPDATED_AT = 'updated_at';

    const EQUALS = ' = ';

    const NOW_JST = 'DATE_ADD(NOW(),INTERVAL 9 HOUR)';

    public function __construct(Builder $builder)
    {
        $this->from = $builder->from;
        parent::__construct($builder->getConnection());
    }

    /**
     * インスタンス生成
     *
     * @param Builder $builder
     * @return self
     */
    public static function build(Builder $builder): self
    {
        return app()->makeWith(self::class, ['builder' => $builder]);
    }

    /**
     * バルクインサート処理
     * key制約発生時はupdateする
     *
     * @param array|Collection $values
     * @param array            $update_values
     * @return void
     */
    public function execute($values, array $update_columns): void
    {
        $values = $this->assembleValues($values);

        $update_columns = $this->generateUpdateColumns($update_columns);

        $query = $this->assembleBulkInsertQuery($values, $update_columns);

        // Execute the created SQL
        $this->connection->insert($query, $this->cleanBindings(Arr::flatten($values, 1)));
    }

    /**
     * もしバルクインサートメソッドに渡す引数が配列かコレクション以外になっていた場合、異常終了させる
     *
     * @param  mixed $values
     * @throws InvalidArgumentException
     * @return void
     */
    public function validateValues($values): void
    {
        if ($this->isCollectionOrModel($values) || is_array($values)) {
            return;
        } else {
            throw new InvalidArgumentException(
                'Invalid argument given. BulkInsertBuilder expects only types of Array or Collection.'
            );
        }
    }

    /**
     * 引数がモデルまたはコレクションかどうか判定
     *
     * @param  mixed $v
     * @return bool
     */
    private function isCollectionOrModel($v): bool
    {
        return $v instanceof Collection
            || get_parent_class($v) === AbstractModelBase::class
            || get_parent_class($v) === Model::class;
    }

    /**
     * valuesをクエリ生成用に加工する
     *
     * @param  array|Collection $v
     * @return array
     */
    private function assembleValues($v): array
    {
        $v = $this->convertValuesIntoArray($v);
        return $this->sortValuesOrCastSingleValueToArray($v);
    }

    /**
     * もしコレクションかモデルインスタンスが引数に渡されていたら、インスタンスを配列にキャストする
     * また、2次元配列内にstdClassが格納されていた場合、全て配列にキャストする
     *
     * @param  array|Collection|Model $v
     * @return array
     */
    private function convertValuesIntoArray($v): array
    {
        $v = $this->isCollectionOrModel($v) ? $v->toArray() : $v;
        return $this->castStdClassToArrayIfGiven($v);
    }

    /**
     * 2次元配列内に格納されている値がstdClassだった場合、配列にキャストする
     *
     * @param  array $values
     * @return array
     */
    private function castStdClassToArrayIfGiven(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_object($value)) {
                $values[$key] = (array) $value;
            }
        }
        return $values;
    }

    /**
     * Here, we will sort the insert keys for every record so that each insert is
     * in the same order for the record. We need to make sure this is the case
     * so there are not any errors or problems when inserting these records.
     *
     * @param array $values
     * @return array
     */
    private function sortValuesOrCastSingleValueToArray(array $values): array
    {
        if (!is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }
        return $values;
    }

    /**
     * updateするカラムを保持する配列を生成する
     *
     * @param array $columns
     * @return Collection
     */
    private function generateUpdateColumns(array $columns): Collection
    {
        return collect($columns)->mapWithKeys(function ($column) {
            // updated_atのみ、更新された時刻を入れるようにする
            return $column === self::UPDATED_AT
                ? [$column => DB::raw(self::NOW_JST)]
                : [$column => DB::raw("VALUES($column)")];
        });
    }

    /**
     * Generate an ordinal INSERT statement and following ON DUPLICATE...
     * clause then join two SQL parts together.
     *
     * @param array $values
     * @param Collection $update_columns
     * @return string
     */
    private function assembleBulkInsertQuery(array $values, Collection $update_columns): string
    {
        $query = $this->grammar->compileInsert($this, $values);

        return $query .= self::DUPLICATE . collect($update_columns)->map(function ($value, $key) {
            return $this->grammar->wrap($key) . self::EQUALS . $this->grammar->parameter($value);
        })->implode(', ');
    }
}
