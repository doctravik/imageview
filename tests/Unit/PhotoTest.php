<?php

namespace Tests\Unit;

use App\User;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PhotoTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_persist_photo_attributes_in_the_db()
    {
        $user = factory(User::class)->create();
        
        $photos = [
            ['path' => '/images/photo1.jpg', 'user_id' => $user->id],
            ['path' => '/images/photo2.jpg', 'user_id' => $user->id],
        ];

        Photo::persist($photos);

        $this->assertCount(2, Photo::all());
        $this->assertDatabaseHas('photos', ['path' => '/images/photo1.jpg', 'user_id' => $user->id]);
        $this->assertDatabaseHas('photos', ['path' => '/images/photo2.jpg', 'user_id' => $user->id]);
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
