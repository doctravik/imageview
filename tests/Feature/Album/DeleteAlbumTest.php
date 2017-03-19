<?php

namespace Tests\Feature\Album;

use App\User;
use App\Album;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteAlbumTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function unauthenticated_user_can_not_delete_album()
    {
        $album = factory(Album::class)->create();

        $response = $this->delete("/admin/albums/{$album->slug}");

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('albums', $album->toArray());
    }

    /** @test */
    public function it_can_delete_album()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create(['name' => 'project']);

        $response = $this->actingAs($user)->delete("/admin/albums/{$album->slug}");

        $response->assertRedirect('/admin/albums');
        $response->assertDontSee($album->name);
        $this->assertCount(0, Album::all());
    }
}
