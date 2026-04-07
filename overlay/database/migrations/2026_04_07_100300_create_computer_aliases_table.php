<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('computer_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('computer_id')->constrained()->cascadeOnDelete();
            $table->string('alias', 190);
            $table->timestamps();

            $table->unique(['computer_id', 'alias']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('computer_aliases');
    }
};
