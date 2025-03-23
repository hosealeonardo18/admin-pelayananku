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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->string('database_name');
            $table->string('package_uid');

            $table->foreign('package_uid')->references('uid')->on('packages');

            $table->timestamps();
        });

        Schema::create('users_tenant', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('user_uid');
            $table->string('tenant_uid');


            $table->foreign('user_uid')->references('uid')->on('users');
            $table->foreign('tenant_uid')->references('uid')->on('tenants');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
