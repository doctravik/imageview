<?php

namespace Tests\Feature\Welcome;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WelcomePageTest extends TestCase
{
    /** @test */
    public function view_has_users()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
