<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->text('content');
            $table->string('source');
            $table->string('category')->default('news');
            $table->string('keywords')->nullable();
            $table->json('authors')->nullable();
            // used to store URLs because some URLs were too long.
            $table->text('url');
            $table->text('image_url')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
