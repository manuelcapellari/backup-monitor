<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('raw_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('mail_account_id')->constrained('mail_accounts')->cascadeOnDelete();
            $table->string('message_uid', 120);
            $table->string('message_id', 255)->nullable();
            $table->string('subject', 500)->nullable();
            $table->string('from_address', 255)->nullable();
            $table->timestamp('received_at')->nullable();
            $table->json('headers_json')->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->enum('ingest_status', ['new', 'parsed', 'ignored', 'error'])->default('new');
            $table->text('ingest_error')->nullable();
            $table->timestamps();

            $table->unique(['mail_account_id', 'message_uid']);
            $table->index(['tenant_id', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_emails');
    }
};
