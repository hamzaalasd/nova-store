<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_banners', function (Blueprint $table): void {
            $table->boolean('show_text_overlay')->default(true)->after('accent_color');
        });
    }

    public function down(): void
    {
        Schema::table('home_banners', function (Blueprint $table): void {
            $table->dropColumn('show_text_overlay');
        });
    }
};
