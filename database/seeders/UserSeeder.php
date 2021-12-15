<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'            => 'epayco',
            'document'            => '123',
            'tlf'            => '000',
            'email'           => 'epaycomailtest@gmail.com',
            'password'        => bcrypt('password'),          
        ]);
      
    }
}