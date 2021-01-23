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
     * レスポンス用のエラー情報をまとめる
     *
     * @param Exception $e
     * @return array
     */
    public function makeErrorInfo(Exception $e): array;
}