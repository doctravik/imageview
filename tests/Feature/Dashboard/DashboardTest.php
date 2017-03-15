<?php

namespace Tests\Feature\Dashboard;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DashboardTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_visit_own_dashboard()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertStatus(200);
        $response->assertViewHas('album');
    }

    /** @test */
    public function unauthenticated_user_cannot_visit_dashboard()
    {
        $response = $this->get('/home');

        $response->assertRedirect('/login');
    }
}
