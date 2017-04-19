<?php

namespace Tests\Feature\Photo;

use App\User;
use App\Album;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ValidateUploadedPhotoTest extends TestCase
{
    use DatabaseTransactions;
    
    public function setUp()
    {
        parent::setUp();

        $user = factory(User::class)->create(['active' => true]);
        $this->actingAs($user);
        $album = factory(Album::class)->create();
        $this->url = "/admin/albums/{$album->slug}/photos";
    }

    /** @test */
    public function it_cannot_validate_request_without_photos()
    {
        $response = $this->from('/home')->post($this->url, [
            'photos' => null
        ]);

        $response->assertRedirect('/home');
        $response->assertSessionHasErrors('photos');
        $this->assertEquals('The photos field is required.', session('errors')->get('photos')[0]);
    }

    /** @test */
    public function it_cannot_validate_request_if_photos_is_empty()
    {
        $response = $this->from('/home')->post($this->url, [
            'photos' => []
        ]);

        $response->assertRedirect('/home');
        $response->assertSessionHasErrors('photos');
        $this->assertEquals('The photos field is required.', session('errors')->get('photos')[0]);
    }

    /** @test */
    public function it_cannot_validate_request_if_photos_is_not_array()
    {
        $response = $this->from('/home')->post($this->url, [
            'photos' => 'photos'
        ]);

        $response->assertRedirect('/home');
        $response->assertSessionHasErrors('photos');
        $this->assertEquals('The photos must be an array.', session('errors')->get('photos')[0]);
    }

    /** @test */
    public function it_cannot_validate_request_if_photo_is_not_image()
    {
        $response = $this->from('/home')->post($this->url, [
            'photos' => [UploadedFile::fake()->create('document.pdf')]
        ]);

        $response->assertRedirect('/home');
        $response->assertSessionHasErrors('photos.0');
        $this->assertEquals('Only jpeg, png and bmp images are allowed.', session('errors')->get('photos.0')[0]);
    }

    /** @test */
    public function it_cannot_validate_request_if_photo_is_more_than_2MB()
    {
        $response = $this->from('/home')->post($this->url, [
            'photos' => [UploadedFile::fake()->image('image.png')->size(2001)]
        ]);

        $response->assertRedirect('/home');
        $response->assertSessionHasErrors('photos.0');
        $this->assertEquals('Maximum allowed size for one image is 2MB.', session('errors')->get('photos.0')[0]);
    }

    /** @test */
    public function it_cannot_validate_request_if_one_file_is_invalid()
    {
        $response = $this->from('/home')->post($this->url, [
            'photos' => [
                UploadedFile::fake()->image('image.JPG'),
                UploadedFile::fake()->image('image.png')->size(2001)
            ]
        ]);

        $response->assertRedirect('/home');
        $response->assertSessionHasErrors('photos.1');
        $this->assertEquals('Maximum allowed size for one image is 2MB.', session('errors')->get('photos.1')[0]);
    }

    /** @test */
    public function it_cannot_validate_request_if_number_of_photos_is_more_than_5()
    {
        $response = $this->from('/home')->post($this->url, [
            'photos' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
                UploadedFile::fake()->image('image3.jpg'),
                UploadedFile::fake()->image('image4.jpg'),
                UploadedFile::fake()->image('image5.jpg'),
                UploadedFile::fake()->image('image6.jpg')
            ]
        ]);

        $response->assertRedirect('/home');
        $response->assertSessionHasErrors('photos');
        $this->assertEquals('The photos may not have more than 5 items.', session('errors')->get('photos')[0]);
    }
}
