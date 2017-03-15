<?php

namespace Tests\Feature\Photo;

use App\User;
use App\Photo;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeletePhotoTest extends TestCase
{
    use DatabaseTransactions;
    
    /** @test */
    public function it_can_delete_uploaded_image_when_photo_is_deleted()
    {
        $storage = $this->fakeStorage();
        $this->actingAs(factory(User::class)->create());
        Photo::upload(UploadedFile::fake()->image('photo1.jpg'));

        $this->assertNotNull(1, $photo = Photo::first());
        $storage->assertExists($photo->path);

        $photo->delete();
        
        $this->assertCount(0, $photos = Photo::all());
        $this->assertCount(0, $storage->allFiles());
    }
}
