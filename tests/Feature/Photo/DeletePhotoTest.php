<?php

namespace Tests\Feature\Photo;

use App\User;
use App\Photo;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeletePhotoTest extends TestCase
{
    private $storage;

    public function setUp()
    {
        parent::setUp();

        Storage::fake('album');
        $this->storage = Storage::disk('album');
        config(['filesystems.default' => 'album']);
    }

    /** @test */
    public function it_can_delete_uploaded_image_when_photo_is_deleted()
    {
        $this->actingAs(factory(User::class)->create());
        Photo::upload([UploadedFile::fake()->image('photo1.jpg')]);

        $this->assertCount(1, $photos = Photo::all());
        $this->storage->assertExists($photos->first()->path);

        $photos->first()->delete();
        
        $this->assertCount(0, $photos = Photo::all());
        $this->assertCount(0, $this->storage->allFiles());
    }


}
