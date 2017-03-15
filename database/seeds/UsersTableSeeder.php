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
        $this->removeOldUsers();

        factory(App\User::class, 5)->create();
    }

    /**
     * Remove all users.
     *
     * @return void
     */
    protected function removeOldUsers()
    {
        \DB::table('users')->delete();
    }
}
