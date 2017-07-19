<?php

namespace Tests\Feature\Webapi;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ValidateUploadPhotoTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $user = factory(User::class)->create(['active' => true]);
        $this->actingAs($user);
        $this->album = factory(Album::class)->create(['user_id' => $user->id]);
        $this->url = "/webapi/albums/{$this->album->id}/photos";
    }

    /** @test */
    public function it_cannot_validate_request_without_photo()
    {
        $response = $this->json('post', $this->url, [
            'photo' => null
        ]);

        $response->assertStatus(422);
        $response->assertJson(['photo' => ['The photo field is required.']]);
    }

    /** @test */
    public function it_cannot_validate_request_if_single_photo_is_not_image()
    {
        $response = $this->json('post', $this->url, [
            'photo' => UploadedFile::fake()->create('document.pdf')
        ]);

        $response->assertStatus(422);
        $response->assertJson(['photo' => ['The photo must be a file of type: jpg, jpeg, png, bmp.']]);
    }

    /** @test */
    public function it_cannot_validate_request_if_single_photo_is_more_than_2MB()
    {
        $response = $this->json('post', $this->url, [
            'photo' => UploadedFile::fake()->image('image.png')->size(2049)
        ]);

        $response->assertStatus(422);
        $response->assertJson(['photo' => ['The photo may not be greater than 2048 kilobytes.']]);
    }

    /** @test */
    public function it_cannot_validate_request_if_user_will_have_more_than_five_photos()
    {
        $photo = factory(Photo::class, 5)->create(['album_id' => $this->album->id]);

        $response = $this->json('post', $this->url, [
            'photo' => UploadedFile::fake()->image('image.jpg')->size(300)
        ]);

        $response->assertStatus(422);
        $response->assertJson(['photo' => ['User cannot upload more than five photos']]);
    }
}
