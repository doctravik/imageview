<?php

namespace Tests\Unit\Image;

use App\Photo;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UrlHelperTest extends TestCase
{
    use DatabaseTransactions;
    
    /** @test */
    public function it_can_get_path_by_size()
    {
        $photo = factory(Photo::class)->create(['path' => 'images/avatar.jpg']);

        $this->assertEquals('images/avatar_small.jpg', $photo->path('small'));
        $this->assertEquals('images/avatar_medium.jpg', $photo->path('medium'));
        $this->assertEquals('images/avatar_large.jpg', $photo->path('large'));
    }

    /** @test */
    public function it_can_get_url_by_size()
    {
        $photo = factory(Photo::class)->create(['path' => 'images/avatar.jpg']);

        $this->assertEquals(Storage::url('images/avatar_small.jpg'), $photo->small());
        $this->assertEquals(Storage::url('images/avatar_medium.jpg'), $photo->medium());
        $this->assertEquals(Storage::url('images/avatar_large.jpg'), $photo->large());
    }

    /** @test */
    public function it_can_return_local_url()
    {
        config(['filesystems.default' => 'local']);
        $photo = factory(Photo::class)->create(['path' => 'images/logo.png']);

        $this->assertEquals('/storage/images/logo.png', $photo->url());
    }

    /** @test */
    public function it_can_return_public_url()
    {
        config(['filesystems.default' => 'public']);
        $photo = factory(Photo::class)->create(['path' => 'images/logo.png']);

        $this->assertEquals(url('/storage/images/logo.png'), $photo->url());
    }

    /** @test */
    // public function it_can_return_s3_url()
    // {
    //     config(['filesystems.default' => 's3']);
    //     $photo = factory(Photo::class)->create(['path' => 'images/logo.png']);
        
    //     $this->assertEquals(
    //         sprintf('https://s3.%s.amazonaws.com/%s/images/logo.png', env('AWS_REGION'), env('AWS_BUCKET')),
    //         $photo->url()
    //     );
    // }
}
