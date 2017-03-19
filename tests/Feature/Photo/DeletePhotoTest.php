<?php

namespace Tests\Feature\Photo;

use App\User;
use App\Photo;
use App\Thumbnail;
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
        $this->actingAs(factory(User::class)->create());
        $photo = Photo::upload(UploadedFile::fake()->image('photo1.jpg'));
        $thumbnail = Thumbnail::make($photo->path)->resize()->save();

        $this->assertNotNull($photo);
        $this->assertNotNull($thumbnail);
        $this->storage->assertExists($photo->path);
        $this->storage->assertExists($thumbnail->getPath());

        $photo->delete();
        
        $this->assertCount(0, $photos = Photo::all());
        $this->assertCount(0, $this->storage->allFiles());
    }
}
