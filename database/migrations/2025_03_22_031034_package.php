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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->double('pricing');
            $table->integer('limit_user')->nullable();
            $table->integer('limit_event')->nullable();
            $table->integer('limit_event_template')->nullable();
            $table->integer('limit_financial_statement')->nullable();
            $table->integer('limit_inventory')->nullable();
            $table->timestamps();
        });

        Schema::create('package_details', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('icon');
            $table->string('package_uid');

            $table->foreign('package_uid')->references('uid')->on('packages');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
