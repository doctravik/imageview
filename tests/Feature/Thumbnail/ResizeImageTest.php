<?php

namespace Tests\Feature\Thumbnail;

use App\Thumbnail;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ResizeImageTest extends TestCase
{
    use DatabaseTransactions;
    
    private $storage;

    public function setUp()
    {
        parent::setUp();

        Storage::fake('album');
        $this->storage = Storage::disk('album');
        config(['filesystems.default' => 'album']);      
    }

    /** @test */
    public function it_can_resize_image()
    {
        $thumbnail = factory(Thumbnail::class)->create(['width' => 300, 'height' => 300]);

        $file = UploadedFile::fake()->image('photo.jpg', 900, 600);
        $filepath = $file->store($file);

        $resize = $thumbnail->copy($filepath)->resize();

        $this->assertTrue($resize);
        $this->storage->assertExists($thumbnail->path);

        $image = Image::make($this->storage->get($thumbnail->path));
        $this->assertEquals(300,  $image->width());
        $this->assertEquals(200,  $image->height());
    }
}
