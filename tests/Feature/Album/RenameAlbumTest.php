<?php

namespace Tests\Feature\Album;

use App\User;
use App\Album;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RenameAlbumTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function not_authenticated_user_cant_rename_album()
    {
        $album = factory(Album::class)->create(['name' => 'nature']);

        $response = $this->patch("/admin/albums/{$album->id}", [
            'name' => 'foods'
        ]);

        $response->assertRedirect('login');
        $this->assertEquals('nature', $album->fresh()->name);
    }

    /** @test */
    public function not_authorized_user_cant_rename_album()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['name' => 'nature']);

        $response = $this->actingAs($user)->patch("/admin/albums/{$album->id}", [
            'name' => 'foods'
        ]);

        $response->assertStatus(403);
        $this->assertEquals('nature', $album->fresh()->name);
    }

    /** @test */
    public function not_active_user_cant_rename_album()
    {
        $user = factory(User::class)->create(['active' => false]);
        $album = factory(Album::class)->create(['user_id' => $user->id, 'name' => 'nature']);

        $response = $this->actingAs($user)->patch("/admin/albums/{$album->id}", [
            'name' => 'foods'
        ]);

        $response->assertRedirect('/account/confirm');
        $this->assertEquals('nature', $album->fresh()->name);
    }

    /** @test */
    public function authorized_user_can_rename_own_album()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id, 'name' => 'nature']);

        $response = $this->actingAs($user)->patch("/admin/albums/{$album->id}", [
            'name' => 'foods'
        ]);

        $response->assertRedirect('/admin/albums');
        $this->assertEquals('foods', $album->fresh()->name);
    }

    /** @test */
    public function authorized_user_can_not_rename_own_album_with_empty_value()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id, 'name' => 'nature']);

        $response = $this->actingAs($user)->patch("/admin/albums/{$album->id}", [
            'name' => ''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('name');
        $this->assertEquals('nature', $album->fresh()->name);
    }

    /** @test */
    public function authorized_user_can_see_rename_form()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id, 'name' => 'nature']);

        $response = $this->actingAs($user)->get("/admin/albums/{$album->slug}/edit");

        $response->assertStatus(200);
        $response->assertViewHas('album');
        $response->assertSee($album->name);
    }
}
