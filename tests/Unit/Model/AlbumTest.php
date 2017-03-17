<?php

namespace Tests\Unit\Model;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlbumTest extends TestCase
{
    use DatabaseTransactions;
   
   /** @test */
    public function it_can_create_slug_for_album()
    {
        $album = Album::create([
            'name' => 'avatar',
            'user_id' => factory(User::class)->create()->id 
        ]);

        $this->assertEquals('avatar', $album->slug);
    }

    /** @test */
    public function it_can_add_photo_to_album()
    {
        $photo = factory(Photo::class)->create();
        $album = factory(Album::class)->create();

        $album->addPhoto($photo);

        $this->assertEquals($photo->album_id, $album->id);
    }

    /** @test */
    public function it_can_get_url_for_album()
    {
        $album = factory(Album::class)->create();

        $this->assertEquals(url("/admin/albums/{$album->slug}"), $album->url());
    }
}
