<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $this->removeExistingUsers();
        
        factory(App\User::class, 5)->create()->each(function ($user) use ($faker) {
            for($i = 0; $i < 6; $i++) {
                $image = $faker->image(public_path('storage/images'), 1280, 1280);

                (new App\Photo)::create([
                    'path' => 'images/'  . basename($image),
                    'user_id' => $user->id
                ]);
            }
        });
    }

    /**
     * Remove all users.
     *
     * @return void
     */
    protected function removeExistingUsers()
    {
        \DB::table('users')->delete();
    }
}
