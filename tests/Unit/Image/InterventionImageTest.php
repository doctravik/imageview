<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Image\InterventionImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InterventionImageTest extends TestCase
{
    /** @test */
    public function it_can_create_invervention_image_instance_from_path()
    {
        $path = UploadedFile::fake()->image('avatar.jpg')->store('images');

        $imageHandler = (new InterventionImage)->make($this->storage->get($path));

        $this->assertInstanceOf(\Intervention\Image\Image::class, $imageHandler->getImage());
    }

    /** @test */
    public function it_can_resize_intervention_image()
    {
        $file = UploadedFile::fake()->image('photo.jpg', 900, 600);

        $intervention = new InterventionImage;
        $image = $intervention->make($file);

        $intervention->resize(450, 300);

        $this->assertEquals(450,  $image->width());
        $this->assertEquals(300,  $image->height());
    }
}
