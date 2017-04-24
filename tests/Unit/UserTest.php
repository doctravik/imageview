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

    /** @test */
    public function user_can_find_own_album_by_slug()
    {
        $user = factory(User::class)->create();
        $userAlbum = factory(Album::class)->create(['name' => 'nature', 'user_id' => $user->id]);
        $anotherAlbum = factory(Album::class)->create(['name' => 'foods']);

        $album = $user->findAlbumBySlug('nature');

        $this->assertEquals($album->id, $userAlbum->id);
    }

    /** 
     * @test
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function it_throw_exception_if_user_cant_find_own_album()
    {
        $user = factory(User::class)->create();

        $album = $user->findAlbumBySlug('nature');
    }
}
