<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
		    'name' => 'محمد عبد الله',
		    'email' => 'admin@example.com',
		    'password' => '123456',
        ]); 
    }
}
