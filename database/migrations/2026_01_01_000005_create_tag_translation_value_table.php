<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tag_translation_value', function (Blueprint $table): void {
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->foreignId('translation_value_id')->constrained('translation_values')->cascadeOnDelete();
            $table->primary(['tag_id', 'translation_value_id']);
            $table->index(['translation_value_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_translation_value');
    }
};
