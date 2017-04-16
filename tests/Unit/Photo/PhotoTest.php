<?php

namespace Tests\Unit\Photo;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PhotoTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_create_photo_from_path()
    {
        $photo = Photo::createFromPath('images/avatar.jpg');

        $this->assertEquals('avatar.jpg', $photo->name);
        $this->assertEquals('images/avatar.jpg', $photo->path);
    }

    /** @test */
    public function it_can_create_slug_for_photo()
    {
        $photo = Photo::create([
            'name' => 'avatar.jpg',
            'path' => 'images/avatar.jpg'
        ]);

        $this->assertEquals('avatar', $photo->slug);
    }

    /** @test */
    public function it_can_upload_file_without_album()
    {
        $photo = Photo::upload(UploadedFile::fake()->image('images/avatar.jpg'));

        $this->storage->assertExists($photo->path);
        $this->assertNull($photo->album_id);
        $this->assertDatabaseHas('photos', $photo->toArray());
    }

    /** @test */
    public function it_can_upload_file_with_album()
    {
        $album = factory(Album::class)->create();
        $photo = Photo::upload(UploadedFile::fake()->image('images/avatar.jpg'), $album);

        $this->storage->assertExists($photo->path);
        $this->assertEquals($photo->album_id, $album->id);
        $this->assertDatabaseHas('photos', $photo->toArray());
    }

    /** @test */
    public function it_check_if_photo_is_private_by_default()
    {
        $photo = factory(Photo::class)->create();

        $this->assertFalse($photo->isPublic());
    }

    /** @test */
    public function it_can_make_photo_public()
    {
        $photo = factory(Photo::class)->create();

        $photo->makePublic();

        $this->assertTrue($photo->isPublic());
    }

    /** @test */
    public function it_check_if_photo_is_avatar_by_default()
    {
        $photo = factory(Photo::class)->create();

        $this->assertFalse($photo->isAvatar());
    }

    /** @test */
    public function it_can_toggle_avatar_property()
    {
        $photo = factory(Photo::class)->create();

        $photo->toggleAvatar();

        $this->assertTrue($photo->isAvatar());
    }

    /** @test */
    public function it_can_reset_avatars_property_to_all_photos_in_album()
    {
        $album = factory(Album::class)->create();
        $photoOne = factory(Photo::class)->create(['album_id' => $album->id, 'is_avatar' => true]);
        $photoTwo = factory(Photo::class)->create(['album_id' => $album->id, 'is_avatar' => false]);
        $photoThree = factory(Photo::class)->create(['is_avatar' => true ]);

        $album->resetAvatars();

        $this->assertFalse($photoOne->fresh()->isAvatar());
        $this->assertFalse($photoTwo->fresh()->isAvatar());
        $this->assertTrue($photoThree->fresh()->isAvatar());
    }
}
