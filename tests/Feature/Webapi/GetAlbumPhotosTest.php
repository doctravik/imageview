<?php

namespace Tests\Feature\Webapi;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GetAlbumPhotosTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function authorized_user_can_get_all_photos_of_own_album()
    {
        $user = factory(User::class)->create();
        $notOwnAlbum = factory(Album::class)->create();
        $ownAlbum = factory(Album::class)->create(['user_id' => $user->id]);
        $notOwnPhoto = factory(Photo::class)->create(['album_id' => $notOwnAlbum->id]);
        $ownPhoto = factory(Photo::class)->create(['album_id' => $ownAlbum->id]);

        $response = $this->actingAs($user)->json('get', "/webapi/albums/{$ownAlbum->slug}/photos");

        $response->assertStatus(200);
        $response->assertExactJson(['data' => [
            array_except($ownPhoto->toArray(), ['created_at', 'updated_at', 'album_id'])
        ]]);
    }

    /** @test */
    public function nonauthenticated_user_cannot_get_all_photos_of_album()
    {
        $album = factory(Album::class)->create();

        $response = $this->json('get', "/webapi/albums/{$album->slug}/photos");

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthorized_user_cannot_get_all_photos_of_album()
    {
        $unauthorizedUser = factory(User::class)->create();
        $album = factory(Album::class)->create();

        $response = $this->actingAs($unauthorizedUser)->json('get', "/webapi/albums/{$album->slug}/photos");

        $response->assertStatus(403);
    }
}
