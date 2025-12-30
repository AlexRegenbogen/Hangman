<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('word', function ($table): void {
            $table->string('locale')->after('id')->length(2);
        });
    }

    public function down(): void
    {
        Schema::table('word', function ($table): void {
            $table->dropColumn('locale');
        });
    }
};
