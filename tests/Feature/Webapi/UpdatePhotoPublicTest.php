<?php

namespace Tests\Feature\Webapi;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdatePhotoPublicTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function unauthenticated_user_cannot_update_public_property()
    {
        $album = factory(Album::class)->create();
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->json('patch', "/webapi/photos/{$photo->slug}", [
            'is_public' => true
        ]);

        $response->assertStatus(403);
        $this->assertFalse($photo->fresh()->isPublic());
    }

    /** @test */
    public function unauthorized_user_cannot_update_public_property()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create();
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/photos/{$photo->slug}", [
            'is_public' => true
        ]);

        $response->assertStatus(403);
        $this->assertFalse($photo->fresh()->isPublic());
    }

    /** @test */
    public function nonactive_user_cannot_update_public_property()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create();
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/photos/{$photo->slug}", [
            'is_public' => true
        ]);

        $response->assertStatus(403);
        $this->assertFalse($photo->fresh()->isPublic());
    }

    /** @test */
    public function authorized_user_can_update_public_property()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/photos/{$photo->slug}", [
            'is_public' => true
        ]);

        $response->assertStatus(200);
        $this->assertTrue($photo->fresh()->isPublic());
    }

    /** @test */
    public function it_cannot_validate_public_property_as_a_string()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/photos/{$photo->slug}", [
            'is_public' => 'hello'
        ]);

        $response->assertStatus(422);
        $response->assertJson(['is_public' => ['The public field must be true or false.']]);
    }

    /** @test */
    public function it_cannot_validate_empty_public_property()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $photo = factory(Photo::class)->create(['album_id' => $album->id]);

        $response = $this->actingAs($user)->json('patch', "/webapi/photos/{$photo->slug}", [
            'is_public' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJson(['is_public' => ['The public field is required.']]);
    }
}
