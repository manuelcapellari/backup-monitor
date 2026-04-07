<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parser_rules', function (Blueprint $table) {
            $table->unsignedInteger('priority')->default(100)->after('status');
            $table->index(['is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('parser_rules', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'priority']);
            $table->dropColumn('priority');
        });
    }
};
