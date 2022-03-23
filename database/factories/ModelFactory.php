<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->defineAs(User::class, 'Admin', function (Faker $faker) {
    return [
        'username' => 'admin',
        'first_name' => 'admin',
        'last_name' => 'admin',
        'nick_name' => 'admin',
        'email' => 'admin@gmail.com',
        'gender' => 1,
        'birthday' => '1990-10-10',
        'description' => 'Administrator',
        'role_id' => 1,
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now()
    ];
});