<?php

namespace Tests\Feature\Album;

use App\User;
use App\Album;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewAlbumsTest extends TestCase
{
    use DatabaseTransactions;
    
    /** @test */
    public function authenticated_user_can_see_all_albums()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/admin/albums');

        $response->assertStatus(200);
        $response->assertViewHas('albums');
    }

    /** @test */
    public function unauthenticated_user_cannot_see_all_albums()
    {
        $response = $this->get('/admin/albums');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_see_single_album()
    {
        $album = factory(Album::class)->create();
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get("/albums/{$album->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('album');
        $response->assertSee($album->name);
    }

    /** @test */
    public function unauthenticated_user_can_see_single_album()
    {
        $album = factory(Album::class)->create();

        $response = $this->get("/albums/{$album->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('album');
        $response->assertSee($album->name);
    }

    /** @test */
    public function unauthenticated_user_cannot_view_admin_album()
    {
        $album = factory(Album::class)->create();

        $response = $this->get("/admin/albums/{$album->slug}");

        $response->assertRedirect('login');
    }

    /** @test */
    public function unauthorized_user_cannot_view_admin_album()
    {
        $unauthorizedUser = factory(User::class)->create();
        $album = factory(Album::class)->create();

        $response = $this->actingAs($unauthorizedUser)->get("/admin/albums/{$album->slug}");

        $response->assertStatus(403);
    }

    /** @test */
    public function authorized_user_can_view_admin_album()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/admin/albums/{$album->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('album');
        $response->assertSee($album->name);
    }
}
