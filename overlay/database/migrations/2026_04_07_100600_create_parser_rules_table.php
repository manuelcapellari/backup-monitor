<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parser_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('vendor', 80)->default('custom');
            $table->enum('match_field', ['from', 'subject', 'body']);
            $table->string('match_pattern', 255);
            $table->enum('status', ['success', 'warning', 'error', 'info', 'other_unmatched'])->default('info');
            $table->string('hostname_regex', 255)->nullable();
            $table->string('job_name_regex', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'match_field']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parser_rules');
    }
};
