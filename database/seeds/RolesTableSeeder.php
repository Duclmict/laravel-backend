<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'Admin',
                'description' => 'Admin role',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Manager',
                'description' => 'Manager role',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'User',
                'description' => 'User role',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
