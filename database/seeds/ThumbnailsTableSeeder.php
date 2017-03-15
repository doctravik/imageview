<?php

use App\Thumbnail;
use Illuminate\Database\Seeder;

class ThumbnailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $thumbnails = App\Photo::all()->map(function($photo) {
            $thumbnail = Thumbnail::make($photo);

            return $thumbnail->toArray();
        });

        Thumbnail::createAll($thumbnails->toArray());
    }
}
