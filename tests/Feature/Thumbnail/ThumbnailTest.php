<?php

namespace Tests\Feature\Thumbnail;

use App\Thumbnail;
use Tests\TestCase;
use App\Image\ImageHandler;
use App\Image\InterventionImage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ResizeImageTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_get_width_and_height_of_thumbnail()
    {
        $path = UploadedFile::fake()->image('photo1.jpg', 600, 900)->store('images');

        $thumbnail  = Thumbnail::make($path);

        $this->assertEquals(600, $thumbnail->width());
        $this->assertEquals(900, $thumbnail->height());
    }

    /** @test */
    public function it_can_make_thumbnail_instance_with_intervention()
    {
        $this->app->bind(ImageHandler::class, function() {
            return new InterventionImage;
        });

        $filepath = UploadedFile::fake()->image('avatar.jpg')->store('images');

        $thumbnail = Thumbnail::make($filepath);

        $this->assertNotNull($thumbnail);
        $this->assertEquals($filepath, $thumbnail->getPath());
        $this->assertTrue($thumbnail->getImageHandler() instanceof InterventionImage);
        $this->assertTrue($thumbnail->image() instanceof \Intervention\Image\Image);        
    }

    /** @test */
    public function it_can_resize_thumbnail()
    {
        $portrait = UploadedFile::fake()->image('photo1.jpg', 600, 900)->store('images');
        $landscape = UploadedFile::fake()->image('photo2.jpg', 900, 600)->store('images');

        $portrait  = Thumbnail::make($portrait)->resize('small');
        $landscape = Thumbnail::make($landscape)->resize('small');

        $this->assertEquals([200, 300], [$portrait->width(), $portrait->height()]);
        $this->assertEquals([300, 200], [$landscape->width(), $landscape->height()]);
    }

    /** @test */
    public function it_can_save_thumbnail()
    {
        $portrait = UploadedFile::fake()->image('photo1.jpg')->storeAs('images', 'photo1.jpg');
        $landscape = UploadedFile::fake()->image('photo2.jpg')->storeAs('images', 'photo2.jpg');

        $portrait  = Thumbnail::make($portrait)->resize('small')->save();
        $landscape = Thumbnail::make($landscape)->resize('small')->save();

        $this->assertCount(4, $this->storage->allFiles());
        $this->storage->assertExists('images/photo1_small.jpg');
        $this->storage->assertExists('images/photo2_small.jpg');
    }
}
