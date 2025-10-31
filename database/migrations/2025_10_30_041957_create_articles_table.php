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
            $table->string('url');
            $table->string('category')->default('news');
            $table->string('keywords')->nullable();
            $table->json('authors')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamp('published_on')->nullable();

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
