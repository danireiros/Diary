<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->string('title');
            $table->text('content')->nullable();
            $table->foreignUuid('diary_category_id')->constrained('diary_categories')->onDelete('cascade');
            $table->boolean('hidden')->default(false);
            $table->timestamps();

            $table->index('date');
            $table->index('diary_category_id');
            $table->index('hidden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};


