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
        Schema::create('player_sport_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->unique()->constrained('players')->cascadeOnDelete();

            $table->foreignId('position_id')->nullable()->constrained('player_positions')->nullOnDelete();
            
            $table->string('jersey_name', 100)->nullable();
            $table->unsignedTinyInteger('jersey_number')->nullable();

            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->unsignedSmallInteger('weight_kg')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_sport_infos');
    }
};
