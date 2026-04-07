<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mail_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 120);
            $table->enum('protocol', ['imap', 'pop3'])->default('imap');
            $table->string('host', 190);
            $table->unsignedSmallInteger('port')->default(993);
            $table->enum('encryption', ['none', 'ssl', 'tls'])->default('tls');
            $table->string('username', 190);
            $table->string('password_encrypted')->nullable();
            $table->string('mailbox', 120)->default('INBOX');
            $table->unsignedSmallInteger('poll_interval_minutes')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_accounts');
    }
};
