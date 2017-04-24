<?php

namespace Tests\Feature\Album;

use App\User;
use App\Album;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateAlbumTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function not_authenticated_user_cant_make_album_public()
    {
        $album = factory(Album::class)->create();

        $response = $this->patch("/admin/albums/{$album->id}", [
            'public' => true
        ]);

        $response->assertRedirect('login');
        $this->assertFalse($album->isPublic());
    }

    /** @test */
    public function not_authorized_user_cant_make_album_public()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create();

        $response = $this->actingAs($user)->patch("/admin/albums/{$album->id}", [
            'public' => true
        ]);

        $response->assertStatus(403);
        $this->assertFalse($album->isPublic());
    }

    /** @test */
    public function not_active_user_cant_make_album_public()
    {
        $user = factory(User::class)->create(['active' => false]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->patch("/admin/albums/{$album->id}", [
            'public' => true
        ]);

        $response->assertRedirect('/account/confirm');
        $this->assertFalse($album->isPublic());
    }

    /** @test */
    public function authorized_user_can_make_album_public()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->patch("/admin/albums/{$album->id}", [
            'public' => true
        ]);

        $response->assertRedirect('/admin/albums');
        $this->assertTrue($album->fresh()->isPublic());
    }
}
