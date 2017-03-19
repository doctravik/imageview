<?php

namespace Tests\Unit\Album;

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
}
