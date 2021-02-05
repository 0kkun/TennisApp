<?php

namespace App\Services\Api;

use Illuminate\Http\Request;
use Exception;

interface ApiServiceInterface
{
    /**
     * APIリクエストのパラメータチェック
     *
     * @param Request $request
     * @param array $keys
     * @return integer
     */
    public function checkArgs(Request $request, array $keys): int;

    /**
     * 処理にかかった時間を算出し桁数調整する
     *
     * @return float
     */
    public function calcTime($start, $end): float;


    /**
     * レスポンス用のエラー情報をまとめる
     *
     * @param Exception $e
     * @return array
     */
    public function makeErrorResponse(Exception $e): array;
}