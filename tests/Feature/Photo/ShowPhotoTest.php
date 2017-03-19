<?php

namespace Tests\Feature\Photo;

use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShowPhotoTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function view_has_photo()
    {
        $photo = factory(Photo::class)->create();

        $response = $this->get("/photos/{$photo->slug}");

        $response->assertViewHas(['photo', 'next', 'prev']);
    }
}
