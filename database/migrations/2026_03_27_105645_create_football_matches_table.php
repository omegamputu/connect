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
        Schema::create('football_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();

            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->cascadeOnDelete();

            $table->string('venue', 150)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();

            $table->string('match_number', 50)->nullable();
            $table->string('external_reference', 100)->nullable();

            $table->date('match_date');
            $table->time('kickoff_time')->nullable();

            $table->enum('status', [
                'scheduled',
                'played',
                'cancelled',
                'postponed',
                'abandoned',
            ])->default('scheduled');

            $table->string('stage', 100)->nullable();
            $table->unsignedTinyInteger('leg')->nullable();

            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->unsignedTinyInteger('home_score_ht')->nullable();
            $table->unsignedTinyInteger('away_score_ht')->nullable();

            $table->boolean('is_neutral_venue')->default(false);
            $table->boolean('is_international')->default(true);

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['competition_id', 'match_date']);
            $table->index(['season_id', 'category_id']);
            $table->index('home_team_id');
            $table->index('away_team_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_matches');
    }
};
