<?php

namespace App\Services\Api;

use Illuminate\Http\Request;
use Exception;

class ApiService implements ApiServiceInterface
{
    protected $result_status;

    public function __construct(
    ){
        $this->result_status = config('api_template.result_status');
    }

    /**
     * APIリクエストのパラメータチェック
     *
     * @param Request $request
     * @param array $keys
     * @return integer
     */
    public function checkArgs(Request $request, array $keys): int
    {
        if ($request->has($keys) && count($request->all()) === count($keys)) {
            return $this->result_status['success'];
        } else {
            return $this->result_status['bad_request'];
        }
    }

    /**
     * レスポンス用のエラー情報をまとめる
     *
     * @param Exception $e
     * @return array
     */
    public function makeErrorInfo(Exception $e): array
    {
        return [
            'message'   => $e->getMessage(),
            'exception' => get_class($e),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
        ];
    }
}