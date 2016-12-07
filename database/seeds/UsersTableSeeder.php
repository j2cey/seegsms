<?php

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
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'SYS',
                'email' => 'sys@here.ga',
                'password' => bcrypt('secretpass'),
                'email_verified' => '1',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],[
                'id' => 2,
                'name' => 'Jude Parfait',
                'email' => 'me@here.ga',
                'password' => bcrypt('secretpass'),
                'email_verified' => '1',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 3,
                'name' => 'M. Gest. Campagnes',
                'email' => 'gestcamp@here.ga',
                'password' => bcrypt('secretpass'),
                'email_verified' => '1',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 4,
                'name' => 'M. Admin. Campagnes',
                'email' => 'admincamp@here.ga',
                'password' => bcrypt('secretpass'),
                'email_verified' => '1',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 5,
                'name' => 'Simple Guy',
                'email' => 'sim@here.ga',
                'password' => bcrypt('secretpass'),
                'email_verified' => '1',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}
