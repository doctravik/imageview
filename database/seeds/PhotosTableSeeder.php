<?php

use App\Photo;
use App\Thumbnail;
use Illuminate\Database\Seeder;

class PhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
        $faker = Faker\Factory::create();
        
        App\Album::all()->each(function($album) use ($faker) {
            for($i = 0; $i < 5; $i++) {
                $image = $faker->image(public_path('storage/images'), 1280, 900);

                $photo = $album->addPhoto(Photo::createFromPath('images/'  . basename($image)));
                Thumbnail::make($photo->path)->resize()->save();
            }
        });
    }
}
