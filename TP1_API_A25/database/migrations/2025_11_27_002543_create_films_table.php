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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('title', 50);
            $table->text('description')->nullable();
            $table->string('release_year', 4)->nullable();
            $table->unsignedBigInteger('language_id');
            $table->unsignedSmallInteger('length')->nullable();
            $table->string('rating', 5)->nullable();
            $table->string('special_features', 200)->nullable();
            $table->string('image', 40)->nullable();
            $table->timestamps();
            $table->foreign('language_id')->references('id')->on('languages')->restrictOnDelete()->restrictOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
