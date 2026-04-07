<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('computers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('hostname', 190);
            $table->string('display_name', 190)->nullable();
            $table->enum('last_status', ['green', 'yellow', 'red', 'gray'])->default('gray');
            $table->timestamp('last_event_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'hostname']);
            $table->index(['tenant_id', 'last_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computers');
    }
};
