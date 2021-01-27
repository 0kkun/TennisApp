<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RankingApiTest extends TestCase
{

    // 正しいリクエストを送った時に正しいレスポンスが返されるか
    /**
     * @test
     */
    public function Api_fetchRankings_正しいリクエストが来たら正しくレスポンスを返すか()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
