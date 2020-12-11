<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            'name' =>'Amar',
            'email' => 'amar@gmail.com',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(60),
        ]);
            


        DB::table('users')->insert([
            'name' => 'Makruf',
            'email' => 'makruf@gmail.com',
            'password' => Hash::make('password'),
            'remember_token' => Str::random(60),
        ]);
    }
}
