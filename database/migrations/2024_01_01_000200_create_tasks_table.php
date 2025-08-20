<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->foreignUuid('category_id')->constrained('categories')->onDelete('cascade');
            $table->enum('status', ['todo','pending','done'])->default('todo');
            $table->boolean('all_day')->default(false);
            $table->dateTimeTz('start_at')->nullable();
            $table->dateTimeTz('end_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->json('recurrence_json')->nullable();
            $table->timestamps();

            $table->index('start_at');
            $table->index('end_at');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};


