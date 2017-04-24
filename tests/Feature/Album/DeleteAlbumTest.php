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

    /** 
     * @test
     * @exception \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function unauthorized_user_can_not_delete_album()
    {
        $unauthorizedUser = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create();

        $response = $this->actingAs($unauthorizedUser)->delete("/admin/albums/{$album->slug}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('albums', $album->toArray());
    }
    
    /** @test */
    public function not_active_user_can_not_delete_album()
    {
        $authorizedUser = factory(User::class)->create();
        $album = factory(Album::class)->create();

        $response = $this->actingAs($authorizedUser)->delete("/admin/albums/{$album->slug}");

        $response->assertRedirect('/account/confirm');
        $this->assertDatabaseHas('albums', $album->toArray());
    }

    /** @test */
    public function authorized_user_can_delete_album()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['name' => 'project', 'user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/admin/albums/{$album->slug}");

        $response->assertRedirect('/admin/albums');
        $response->assertDontSee($album->name);
        $this->assertCount(0, Album::all());
    }
}
