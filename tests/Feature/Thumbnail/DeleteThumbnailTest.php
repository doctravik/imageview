<?php

namespace Tests\Feature\Thumbnail;

use App\Photo;
use App\Thumbnail;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteThumbnailTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_delete_thumbnail_image_when_thumbnail_is_deleted()
    {
        $storage = $this->fakeStorage();
        $photo = Photo::upload(UploadedFile::fake()->image('images/avatar.jpg'));

        Thumbnail::make($photo)->save();

        $this->assertNotNull($thumbnail = Thumbnail::first());
        $storage->assertExists($thumbnail->path);

        $thumbnail->delete();
        
        $this->assertCount(0, $thumbnails = Thumbnail::all());
        $this->assertCount(1, $storage->allFiles());
        $storage->assertMissing($thumbnail->path);
    }

    /** @test */
    public function it_can_delete_thumbnail_and_photo_image_when_photo_is_deleted()
    {
        $storage = $this->fakeStorage();
        $photo = Photo::upload(UploadedFile::fake()->image('images/avatar.jpg'));
        Thumbnail::make($photo)->save();

        $this->assertCount(2, $storage->allFiles());
        $storage->assertExists($photo->path);
        $storage->assertExists(Thumbnail::first()->path);

        $photo->delete();
        
        $this->assertCount(0, Photo::all());
        $this->assertCount(0, Thumbnail::all());
        $this->assertCount(0, $storage->allFiles());
    }
}
