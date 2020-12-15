<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('twitter_username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('token')->unique()->nullable();
            $table->boolean('is_email_validated')->default(false);
            $table->boolean('is_phone_validated')->default(false);
            $table->string('email_validate_code')->nullable();
            $table->integer('phone_validate_code')->nullable();
            $table->softDeletes();
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
