<?php

namespace Tests\Unit\Model;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PhotoTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_insert_several_photo_attributes_into_db_with_one_query()
    {
        $album = factory(Album::class)->create();
        
        $photos = [
            ['name' => 'photo1.jpg', 'path' => '/images/photo1.jpg', 'slug'=> 'photo1', 'album_id' => $album->id],
            ['name' => 'photo2.jpg', 'path' => '/images/photo2.jpg', 'slug'=> 'photo2', 'album_id' => $album->id],
        ];

        Photo::createAll($photos);

        $this->assertCount(2, Photo::all());
        $this->assertDatabaseHas('photos', ['path' => '/images/photo1.jpg', 'album_id' => $album->id]);
        $this->assertDatabaseHas('photos', ['path' => '/images/photo2.jpg', 'album_id' => $album->id]);
    }

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
        $storage = $this->fakeStorage();
        $photo = Photo::upload(UploadedFile::fake()->image('images/avatar.jpg'));

        $storage->assertExists($photo->path);
        $this->assertNull($photo->album_id);
        $this->assertDatabaseHas('photos', $photo->toArray());
    }

    /** @test */
    public function it_can_upload_file_with_album()
    {
        $storage = $this->fakeStorage();
        $album = factory(Album::class)->create();
        $photo = Photo::upload(UploadedFile::fake()->image('images/avatar.jpg'), $album);

        $storage->assertExists($photo->path);
        $this->assertEquals($photo->album_id, $album->id);
        $this->assertDatabaseHas('photos', $photo->toArray());
    }

    /** @test */
    public function it_can_return_local_url_for_product_image()
    {
        config(['filesystems.default' => 'local']);
        $photo = factory(Photo::class)->create(['path' => 'images/logo.png']);

        $this->assertEquals('/storage/images/logo.png', $photo->url());
    }

    /** @test */
    public function it_can_return_public_url_for_product_image()
    {
        config(['filesystems.default' => 'public']);
        $photo = factory(Photo::class)->create(['path' => 'images/logo.png']);

        $this->assertEquals(env('APP_URL') . '/storage/images/logo.png', $photo->url());
    }

    /** @test */
    public function it_can_return_s3_url_for_product_image()
    {
        config(['filesystems.default' => 's3']);
        $photo = factory(Photo::class)->create(['path' => 'images/logo.png']);
        
        $this->assertEquals(
            sprintf('https://s3.%s.amazonaws.com/%s/images/logo.png', env('AWS_REGION'), env('AWS_BUCKET')),
            $photo->url()
        );
    }
}
