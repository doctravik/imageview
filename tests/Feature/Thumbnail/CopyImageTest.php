<?php

namespace Tests\Feature\Thumbnail;

use App\Thumbnail;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CopyImageTest extends TestCase
{
    use DatabaseTransactions;

    private $storage;

    public function setUp()
    {
        parent::setUp();

        Storage::fake('album');
        $this->storage = Storage::disk('album');
        config(['filesystems.default' => 'album']);      
    }

    /** @test */
    public function it_can_copy_file()
    {
        $thumbnail = factory(Thumbnail::class)->create();

        $file = UploadedFile::fake()->image('photo2.png');
        $filepath = $file->store($file);

        $result = $thumbnail->copy($filepath);

        $this->storage->assertExists($thumbnail->path);
        $this->assertTrue($result instanceof Thumbnail);
    }
}
