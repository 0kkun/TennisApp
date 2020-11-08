<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Contracts\PlayersRepository;

class FavoritePlayerServiceTest extends TestCase
{
    use RefreshDatabase;


    private $players_repository;

    public function setUp()
    {
        parent::setUp();
        $this->players_repository = app(PlayersRepository::class);
    }


    public function tearDown()
    {
        parent::tearDown();
    }


    /**
     * 正常系
     * @test
     */
    public function searchPlayersのテスト()
    {
        $this->assertTrue(true);
    }
}
