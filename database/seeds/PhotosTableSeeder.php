<?php

use App\Photo;
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

                $album->addPhoto(Photo::createFromPath('images/'  . basename($image)));
            }
        });
    }
}
