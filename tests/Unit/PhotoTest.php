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
}
