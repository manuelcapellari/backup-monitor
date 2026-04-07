<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parser_traces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_email_id')->constrained('raw_emails')->cascadeOnDelete();
            $table->foreignId('parser_rule_id')->nullable()->constrained('parser_rules')->nullOnDelete();
            $table->boolean('matched')->default(false);
            $table->string('reason', 255)->nullable();
            $table->json('result_json')->nullable();
            $table->timestamps();

            $table->index(['raw_email_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parser_traces');
    }
};
