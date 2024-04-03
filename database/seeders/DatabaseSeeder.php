<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('employees')->insert([
            'name' => Str::random(10),
            'email' => 'employee@gmail.com',
            'password' => Hash::make('password'),
        ]);

        DB::table('hrs')->insert([
            'name' => Str::random(10),
            'email' => 'hr@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
