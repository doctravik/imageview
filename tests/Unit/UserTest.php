<?php

namespace Tests\Unit;

use App\User;
use App\Album;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_has_more_photo_than_limit()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        factory(Photo::class, 5)->create(['album_id' => $album->id]);

        $this->assertTrue($user->isOutOfLimit());
    }

    /** @test */
    public function user_has_less_photo_than_limit()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $this->assertFalse($user->isOutOfLimit());

        factory(Photo::class)->create(['album_id' => $album->id]);
        $this->assertFalse($user->fresh()->isOutOfLimit());

        factory(Photo::class, 3)->create(['album_id' => $album->id]);
        $this->assertFalse($user->fresh()->isOutOfLimit());
    }

    /** @test */
    public function it_can_check_if_user_is_owner_of_album()
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $anotherAlbum = factory(Album::class)->create();

        $this->assertTrue($user->isOwnerOf($album));
        $this->assertFalse($user->isOwnerOf($anotherAlbum));
    }
}
