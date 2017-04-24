<?php

namespace Tests\Feature\Photo;

use App\User;
use App\Album;
use App\Photo;
use App\Thumbnail;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UploadSinglePhotoTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function authorized_user_can_upload_single_photo_with_proper_size()
    {
        $user = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->json('post', "/webapi/albums/{$album->id}/photos", [
            'photo' => UploadedFile::fake()->image('photo1.jpg', 900, 600)
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Photo::all());
        $this->assertCount(2, $this->storage->allFiles());
        $photo = Photo::first();
        $this->storage->assertExists($photo->path);
        $this->storage->assertExists($photo->path('small'));
        $this->assertEquals(900, Image::make($this->storage->get($photo->path))->width());
        $this->assertEquals(600, Image::make($this->storage->get($photo->path))->height());
        $this->assertEquals(300, Image::make($this->storage->get($photo->path('small')))->width());
        $this->assertEquals(200, Image::make($this->storage->get($photo->path('small')))->height());
    }

    /** @test */
    public function unauthenticated_user_can_not_upload_single_photo()
    {
        $album = factory(Album::class)->create();
        
        $response = $this->json('post', "/webapi/albums/{$album->id}/photos", [
            'photo' => UploadedFile::fake()->image('photo1.jpg', 900, 600)
        ]);

        $response->assertStatus(403);
        $this->assertCount(0, $photos = Photo::all());
        $this->assertCount(0, $this->storage->allFiles());
    }

    /** @test */
    public function unauthorized_user_can_not_upload_single_photos()
    {
        $unauthorizedUser = factory(User::class)->create(['active' => true]);
        $album = factory(Album::class)->create();

        $response = $this->actingAs($unauthorizedUser)->json('post', "/webapi/albums/{$album->id}/photos", [
            'photo' => UploadedFile::fake()->image('photo1.jpg', 900, 600)
        ]);

        $response->assertStatus(403);
        $this->assertCount(0, $photos = Photo::all());
        $this->assertCount(0, $this->storage->allFiles());
    }
}
