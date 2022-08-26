<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\User;
use App\Models\Category_post;
use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            RoleSeeder::class
        );
        User::factory(10)->create();
        Post::factory(10)->create();
        Category::factory(10)->create();
        Category_post::factory(10)->create();


        User::create([
            'firstname' => "camado",
            'lastname' => "choaib",
            'email' => "camado@gmail.com",
            'password' => bcrypt("password"),
            'role_id' => 1,
            "picture" => "site_images/user_placeholder.png",
            "username" => "camado",
        ]);
    }
}
