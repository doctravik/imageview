<?php

namespace Tests\Feature\Photo;

use App\User;
use App\Photo;
use App\Thumbnail;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UploadPhotoTest extends TestCase
{
    use DatabaseTransactions;

    protected $storage;

    public function setUp()
    {
        parent::setUp();

        $this->storage = $this->fakeStorage();
    }

    /** @test */
    public function it_can_upload_photos()
    {
        $user = factory(User::class)->create();

        $photos = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.png')
        ];

        $response = $this->actingAs($user)->post('/photo', [
            'photos' => $photos
        ]);

        $response->assertStatus(302);
        $this->assertCount(2, $photos = Photo::all());
        $this->assertCount(2, $thumbnails = Thumbnail::all());
        $photos->each(function($photo) {
            $this->storage->assertExists($photo->path);
        });
        $thumbnails->each(function($thumbnail) {
            $this->storage->assertExists($thumbnail->path);
        });
    }

    /** @test */
    public function unauthenticated_user_can_not_upload_photo()
    {
        $photos = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.png')
        ];

        $response = $this->post('/photo', [
            'photos' => $photos
        ]);

        $response->assertRedirect('/login');
        $this->assertCount(0, $photos = Photo::all());
        $this->assertCount(0, $this->storage->allFiles());
    }
}
