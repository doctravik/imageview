<?php

namespace Tests\Unit\Traits;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use App\Traits\InteractWithInterventionImage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InteractWithInterventionImageTest extends TestCase
{
    use InteractWithInterventionImage;

    /** @test */
    public function it_can_get_parameters_for_resize()
    {
        $portrait = UploadedFile::fake()->image('photo1.jpg', 600, 800);
        $landscape = UploadedFile::fake()->image('photo2.jpg', 800, 600);

        $portraitImage = Image::make($portrait);
        $landscapeImage = Image::make($landscape);
        
        $portraitParameters = $this->getResizeParameters($portraitImage, 250, 250);
        $landscapeParameter = $this->getResizeParameters($landscapeImage, 250, 250);

        $this->assertEquals([null, 250], $portraitParameters);
        $this->assertEquals([250, null], $landscapeParameter);
    }

    /** @test */
    public function image_has_portrait_orientation()
    {
        $portrait = UploadedFile::fake()->image('photo1.jpg', 100, 200);
        $landscape = UploadedFile::fake()->image('photo2.jpg', 200, 100);

        $portraitImage = Image::make($portrait);
        $landscapeImage = Image::make($landscape);

        $this->assertTrue($this->hasPortraitOrientation($portraitImage));
        $this->assertFalse($this->hasPortraitOrientation($landscapeImage));
    }
}
