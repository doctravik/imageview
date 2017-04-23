<?php

namespace Tests\Feature\Webapi;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RankingPhotosTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function not_authenticated_user_cant_sort_photos()
    {
        $album = factory(Album::class)->create();

        $response = $this->json('patch', "/webapi/albums/{$album->slug}/photos/sorting", [
            'photos' => [1, 2]
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthorized_user_cant_sort_photos()
    {   
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create();

        $response = $this->actingAs($user)->json('patch', "/webapi/albums/{$album->slug}/photos/sorting", [
            'photos' => [1, 2]
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function not_active_user_cant_sort_photos()
    {
        $user = factory(User::class)->create(['active' => false]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/albums/{$album->slug}/photos/sorting");

        $response->assertRedirect('/account/confirm');
    }

    /** @test */
    public function sorting_request_doesnt_validate_string_data()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/albums/{$album->slug}/photos/sorting", [
            'photos' => 'photos'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function authorized_active_user_can_sort_photos()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $photoOne = factory(Photo::class)->create(['album_id' => $album->id, 'sort_order' => 1]);
        $photoTwo = factory(Photo::class)->create(['album_id' => $album->id,'sort_order' => 2]);

        $photoOne->sort_order = 2;
        $photoTwo->sort_order = 1;

        $response = $this->actingAs($user)->json('patch', "/webapi/albums/{$album->slug}/photos/sorting", [
            'photos' => [$photoOne->toArray(), $photoTwo->toArray()]
        ]);

        $response->assertStatus(200);
        $this->assertEquals(1, $photoTwo->fresh()->sort_order);
        $this->assertEquals(2, $photoOne->fresh()->sort_order);
    }
}
