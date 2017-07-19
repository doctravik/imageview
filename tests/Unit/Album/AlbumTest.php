<?php

namespace Tests\Unit\Album;

use App\User;
use App\Album;
use App\Photo;
use App\Thumbnail;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlbumTest extends TestCase
{
    use DatabaseTransactions;

   /** @test */
    public function it_can_create_slug_for_album()
    {
        $album = Album::create([
            'name' => 'avatar',
            'user_id' => factory(User::class)->create()->id
        ]);

        $this->assertEquals('avatar', $album->slug);
    }

    /** @test */
    public function it_can_add_photo_to_album()
    {
        $photo = factory(Photo::class)->create();
        $album = factory(Album::class)->create();

        $album->addPhoto($photo);

        $this->assertEquals($photo->album_id, $album->id);
    }

    /** @test */
    public function it_can_get_url_for_album()
    {
        $album = factory(Album::class)->create();

        $this->assertEquals(url("/admin/albums/{$album->slug}"), $album->url());
    }

    /** @test */
    public function it_automatically_remove_all_photos_and_images_when_album_is_deleted()
    {
        $this->actingAs(factory(User::class)->create());
        $album = factory(Album::class)->create();
        $photo = Photo::upload(UploadedFile::fake()->image('images/avatar.jpg'), $album);
        $thumbnail = Thumbnail::make($photo->path)->resize()->save();

        $this->assertNotNull($photo);
        $this->assertNotNull($thumbnail);
        $this->storage->assertExists($photo->path);
        $this->storage->assertExists($thumbnail->getPath());

        $album->delete();

        $this->assertCount(0, $photos = Photo::all());
        $this->assertCount(0, $this->storage->allFiles());
    }
}
