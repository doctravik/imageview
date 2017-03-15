<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Set fake previous url.
     * 
     * @param  string $url
     * @return TestCase
     */
    protected function from($url) 
    {
        $url = env('APP_URL') . $url;
        $this->app['session']->setPreviousUrl($url);

        return $this;
    }

    /**
     * Create fake storage.
     *
     * @param string $name
     * @return Illuminate\Filesystem\FilesystemAdapter
     */
    protected function fakeStorage($name = 'album')
    {
        Storage::fake($name);
        config(['filesystems.default' => $name]);

        return Storage::disk($name);
    }
}
