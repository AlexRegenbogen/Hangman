<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnGameTableGuessed extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game', function (Blueprint $table): void {
            $table->longText('characters_guessed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game', function ($table): void {
            $table->dropColumn('characters_guessed');
        });
    }
}
