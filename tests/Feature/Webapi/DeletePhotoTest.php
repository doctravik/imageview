<?php

namespace Tests\Feature\Webapi;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeletePhotoTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function unauthenticated_ajax_request_cannot_delete_photo_from_album()
    {
        $album = factory(Album::class)->create();
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->json('delete', "/webapi/albums/{$album->slug}/photos/{$photo->slug}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('photos', $photo->toArray());
    }

    /** @test */
    public function unauthorized_ajax_request_cannot_delete_photo_from_album()
    {
        $unauthorizedUser = factory(User::class)->create();
        $album = factory(Album::class)->create();
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($unauthorizedUser)
            ->json('delete', "/webapi/albums/{$album->slug}/photos/{$photo->slug}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('photos', $photo->toArray());
    }

    /** @test */
    public function authorized_ajax_request_can_delete_photo_from_album()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($user)
            ->json('delete', "/webapi/albums/{$album->slug}/photos/{$photo->slug}");

        $response->status(200);
        $this->assertDatabaseMissing('photos', $photo->toArray());
    }

}
