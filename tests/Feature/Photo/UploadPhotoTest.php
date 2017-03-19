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

class UploadPhotoTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_upload_photos()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create();

        $photos = [
            UploadedFile::fake()->image('photo1.jpg', 900, 600),
            UploadedFile::fake()->image('photo2.png', 900, 600)
        ];

        $response = $this->actingAs($user)->post("/admin/albums/{$album->slug}/photos", [
            'photos' => $photos
        ]);

        $response->assertStatus(302);
        $this->assertCount(2, $photos = Photo::all());
        $this->assertCount(4, $this->storage->allFiles());
        $photos->each(function($photo) {
            $this->storage->assertExists($photo->path);
            $this->storage->assertExists($photo->path('small'));
            $this->assertEquals(900, Image::make($this->storage->get($photo->path))->width());
            $this->assertEquals(600, Image::make($this->storage->get($photo->path))->height());
            $this->assertEquals(300, Image::make($this->storage->get($photo->path('small')))->width());
            $this->assertEquals(200, Image::make($this->storage->get($photo->path('small')))->height());
        });
    }

    /** @test */
    public function unauthenticated_user_can_not_upload_photo()
    {
        $album = factory(Album::class)->create();
        
        $photos = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.png')
        ];

        $response = $this->post('/admin/albums/{$album->slug}/photos', [
            'photos' => $photos
        ]);

        $response->assertRedirect('/login');
        $this->assertCount(0, $photos = Photo::all());
        $this->assertCount(0, $this->storage->allFiles());
    }
}
