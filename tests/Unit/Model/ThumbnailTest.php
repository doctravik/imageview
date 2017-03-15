<?php

namespace Tests\Unit\Model;

use App\Photo;
use App\Thumbnail;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThumbnailTest extends TestCase
{
    use DatabaseTransactions;
   
   /** @test */
    public function it_can_create_slug_for_thumbnail()
    {
        $thumbnail = Thumbnail::create([
            'name' => 'avatar.jpg',
            'path' => 'images/avatar.jpg'
        ]);

        $this->assertEquals('avatar', $thumbnail->slug);
    }

    /** @test */
    public function it_can_associate_thumbnail_with_photo()
    {
        $photo = factory(Photo::class)->create();
        $thumbnail = factory(Thumbnail::class)->create();

        $this->assertNotEquals($photo->id, $thumbnail->photo_id);
        $thumbnail->associateWith($photo);

        $this->assertEquals($photo->id, $thumbnail->photo_id);
    }

    /** @test */
    public function it_can_create_thumbnail_from_photo()
    {
        $photo = factory(Photo::class)->create(['path' => 'images/avatar.jpg']);

        $thumbnail = Thumbnail::createFromPhoto($photo, 200, 250);

        $this->assertEquals(200, $thumbnail->width);
        $this->assertEquals(250, $thumbnail->height);
        $this->assertEquals('avatar_200x250.jpg', $thumbnail->name);
        $this->assertEquals('images/avatar_200x250.jpg', $thumbnail->path);
        $this->assertEquals(str_slug('avatar_200x250'), $thumbnail->slug);
    }

    /** @test */
    public function it_can_make_thumbnail_from_photo()
    {
        $storage = $this->fakeStorage();
        $photo = Photo::upload(UploadedFile::fake()->image('images/avatar.jpg'));

        Thumbnail::make($photo)->save();

        $this->assertNotNull($thumbnail = Thumbnail::first());
        $this->assertNotNull($image = Image::make($storage->get($thumbnail->path)));
        $storage->assertExists($photo->path);
        $storage->assertExists($thumbnail->path);
        $this->assertEquals($thumbnail->photo_id, $photo->id);
    }

    /** @test */
    public function it_can_insert_several_thumbnails_into_db_with_one_query()
    {
        $photo = factory(Photo::class)->create();

        $thumbnails = [
            ['path' => 'avatar1.jpg', 'name' => 'avatar1.jpg', 'slug' => 'avatar1', 'photo_id' => $photo->id],
            ['path' => 'avatar2.jpg', 'name' => 'avatar2.jpg', 'slug' => 'avatar2', 'photo_id' => $photo->id]
        ];
        
        Thumbnail::createAll($thumbnails);

        $this->assertCount(2, Thumbnail::all());
    }
}
