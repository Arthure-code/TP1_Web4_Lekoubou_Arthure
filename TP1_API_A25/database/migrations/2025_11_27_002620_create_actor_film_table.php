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
       Schema::create('actor_film', function (Blueprint $table) {
            $table->foreignId('actor_id')
                  ->constrained('actors')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('film_id')
                  ->constrained('films')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->primary(['actor_id', 'film_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actor_film');
    }
};
