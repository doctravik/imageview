<?php

namespace Tests\Feature\Album;

use App\User;
use App\Album;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StoreAlbumTest extends TestCase
{
    use DatabaseTransactions;
    
    /** @test */
    public function unauthenticated_user_cannot_store_album()
    {
        $response = $this->post('/admin/albums', [
            'name' => 'project'
        ]);

        $response->assertRedirect('/login');
        $this->assertCount(0, Album::all());
    }

    /** @test */
    public function not_active_user_cannot_store_album()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/admin/albums', [
            'name' => 'project'
        ]);

        $response->assertRedirect('/account/confirm');
        $this->assertCount(0, Album::all());
    }

    /** @test */
    public function it_cannot_store_album_without_name()
    {
        $user = factory(User::class)->create(['active' => true]);

        $response = $this->actingAs($user)->from('/admin/albums')->post('/admin/albums', [
            'name' => ''
        ]);

        $response->assertRedirect('/admin/albums');
        $response->assertSessionHasErrors('name');
        $this->assertCount(0, Album::all());
    }


    /** @test */
    public function it_cannot_store_album_with_the_same_name()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['name' => 'project']);

        $response = $this->actingAs($user)->from('/admin/albums')->post('/admin/albums', [
            'name' => 'project'
        ]);

        $response->assertRedirect('/admin/albums');
        $response->assertSessionHasErrors('name');
        $this->assertCount(1, Album::all());
    }

    /** @test */
    public function it_can_store_album()
    {
        $user = factory(User::class)->create(['active' => true]);

        $response = $this->actingAs($user)->post('/admin/albums', [
            'name' => 'project'
        ]);

        $response->assertRedirect('/admin/albums');
        $this->assertDatabaseHas('albums', ['name' => 'project']);
    }
}
