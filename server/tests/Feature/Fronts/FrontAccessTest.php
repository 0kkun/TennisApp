<?php

namespace Tests\Feature\Fronts;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Route;
use App\Models\User;

class FrontAccessTest extends TestCase
{
    private static $user;


    public function testLogin()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/login');
        $response->assertStatus(200);
    }


    public function testTopPage()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/');
        $response->assertStatus(200);
    }


    public function testFavoritePlayerPage()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/favorite_player');
        $response->assertStatus(200);
    }


    public function testFavoriteBrandPage()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/favorite_brand');
        $response->assertStatus(200);
    }


    public function testAnalysisPage()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/analysis');
        $response->assertStatus(200);
    }
}