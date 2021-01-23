<?php

return [
    /**
     * APIのレスポンスフォーマット
     */
    'response_format' => [
        'status' => null,
        'data'   => null,
    ],
    /**
     * APIのレスポンスステータスコード
     */
    'result_status' => [
        'success'      => 200,
        'created'      => 201,
        'no_content'   => 204,
        'bad_request'  => 400,
        'server_error' => 500
    ]
];