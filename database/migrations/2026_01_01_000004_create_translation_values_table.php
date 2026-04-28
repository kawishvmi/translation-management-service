<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translation_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('translation_key_id')->constrained('translation_keys')->cascadeOnDelete();
            $table->foreignId('locale_id')->constrained('locales')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();

            $table->unique(['translation_key_id', 'locale_id']);
            $table->index(['locale_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_values');
    }
};
