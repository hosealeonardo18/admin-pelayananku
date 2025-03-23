<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_users', function (Blueprint $table) {
            $table->id();
            $table->uid('uid')->unique();
            $table->string('user_uid');
            $table->string('role_uid');

            $table->foreign('user_uid')->references('uid')->on('users');
            $table->foreign('role_uid')->references('uid')->on('roles');
            $table->timestamps();
        });

        // Role ↔ Permission
        Schema::create('permission_roles', function (Blueprint $table) {
            $table->id();
            $table->uid('uid')->unique();
            $table->string('role_uid');
            $table->string('permission_uid');

            $table->foreign('role_uid')->references('uid')->on('roles');
            $table->foreign('permission_uid')->references('uid')->on('permissions');
            $table->timestamps();
        });

        // User ↔ Permission (override langsung)
        Schema::create('permission_users', function (Blueprint $table) {
            $table->id();
            $table->uid('uid')->unique();
            $table->string('user_uid');
            $table->string('permission_uid');

            $table->foreign('user_uid')->references('uid')->on('users');
            $table->foreign('permission_uid')->references('uid')->on('permissions');
            $table->timestamps();
        });

        // Menu ↔ Role
        Schema::create('menu_roles', function (Blueprint $table) {
            $table->id();
            $table->uid('uid')->unique();
            $table->string('menu_uid');
            $table->string('role_uid');

            $table->foreign('menu_uid')->references('uid')->on('menus');
            $table->foreign('role_uid')->references('uid')->on('roles');
            $table->timestamps();
        });

        // Menu ↔ Permission
        Schema::create('menu_permissions', function (Blueprint $table) {
            $table->id();
            $table->uid('uid')->unique();
            $table->string('menu_uid');
            $table->string('permission_uid');

            $table->foreign('menu_uid')->references('uid')->on('menus');
            $table->foreign('permission_uid')->references('uid')->on('permissons');
            $table->timestamps();
        });

        // Menu ↔ User (override)
        Schema::create('menu_users', function (Blueprint $table) {
            $table->id();
            $table->string('menu_uid');
            $table->string('user_uid');

            $table->foreign('menu_uid')->references('uid')->on('menus');
            $table->foreign('menu_uid')->references('uid')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_users');
    }
};
