<?php

namespace Tests\Feature\Webapi;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdatePhotoAvatarTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function unauthenticated_user_cannot_update_avatar_property()
    {
        $album = factory(Album::class)->create();
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->json('patch', "/webapi/photos/{$photo->slug}/avatars");

        $response->assertStatus(403);
        $this->assertFalse($photo->fresh()->isAvatar());
    }

    /** @test */
    public function unauthorized_user_cannot_update_avatar_property()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create();
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/photos/{$photo->slug}/avatars");

        $response->assertStatus(403);
        $this->assertFalse($photo->fresh()->isAvatar());
    }

    /** @test */
    public function authorized_user_can_update_avatar_property()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/photos/{$photo->slug}/avatars");

        $response->assertStatus(200);
        $this->assertTrue($photo->fresh()->isAvatar());
    }
}
