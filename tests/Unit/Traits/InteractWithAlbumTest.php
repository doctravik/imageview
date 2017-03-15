<?php

namespace Tests\Unit\Traits;

use App\User;
use App\Album;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InteractWithAlbumTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_add_album_to_user()
    {
        $user = factory(User::class)->create();
        $album = new Album();
        $album->name = 'project';

        $user->addAlbum($album);

        $this->assertTrue($user->hasAlbum($album));
    }

    /** @test */
    public function it_can_check_if_user_has_any_albums()
    {
        $user = factory(User::class)->create();
        $this->assertFalse($user->hasAlbums());

        $album = new Album();
        $album->name = 'project';

        $user->addAlbum($album);

        $this->assertTrue($user->fresh()->hasAlbums());                
    }

    /** @test */
    public function it_can_get_first_album_if_it_exists()
    {
        $album = factory(Album::class)->create();
        $user = $album->user()->get()->first();

        $this->assertEquals($album->id, $user->getFirstAlbum()->id);
        $this->assertCount(1, $user->albums);
    }
    
    /** @test */
    public function it_can_create_new_album_on_request()
    {
        $user = factory(User::class)->create();

        $album = $user->createAlbum('new album');

        $this->assertTrue($album instanceof Album);
        $this->assertCount(1, Album::all());
        $this->assertDatabaseHas('albums', ['name' => 'new album']);
    }

    /** @test */
    public function it_can_get_first_album_if_it_doesnt_exists()
    {
        $user = factory(User::class)->create();

        $album = $user->getFirstAlbum();

        $this->assertTrue($album instanceof Album);
        $this->assertCount(1, Album::all());
        $this->assertDatabaseHas('albums', ['name' => str_slug($user->email)]);
    }
}
