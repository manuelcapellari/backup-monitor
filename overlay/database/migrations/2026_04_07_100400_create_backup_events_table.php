<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('backup_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('computer_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['success', 'warning', 'error', 'info', 'other_unmatched']);
            $table->string('summary', 500);
            $table->string('source_vendor', 120)->nullable();
            $table->timestamp('event_at');
            $table->string('raw_email_ref', 190)->nullable();
            $table->timestamps();

            $table->index(['computer_id', 'event_at']);
            $table->index(['status', 'event_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_events');
    }
};
