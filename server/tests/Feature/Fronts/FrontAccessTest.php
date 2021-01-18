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
}