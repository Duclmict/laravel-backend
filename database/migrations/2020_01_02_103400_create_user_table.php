<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned();
            $table->string('username', 100)->nullable();
            $table->string('email', 100);
            $table->string('password', 200);
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('first_name_kana', 50)->nullable();
            $table->string('last_name_kana', 50)->nullable();
            $table->boolean('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->string('nick_name', 50)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('remember_token', 100)->nullable();

            $table->boolean('is_deleted')->default(false);
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
