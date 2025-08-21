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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->text('excerpt')->nullable(); // Short summary for cards, lists, SEO
            $table->string('image')->nullable();
            $table->json('tags')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->dateTime('published_at')->nullable();
            $table->boolean('noindex')->default(false);
            $table->boolean('nofollow')->default(false);

            $table->unsignedInteger('reading_time')->nullable();
            $table->unsignedInteger('word_count')->nullable();

            $table->string('meta_title')->nullable(); // < 60 chars
            $table->string('meta_description', 500)->nullable(); // < 155 chars
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->json('meta_tags')->nullable();
            $table->json('og_tags')->nullable();
            $table->json('structured_data')->nullable(); // to store dynamic BlogPosting schema or other types, then render directly in Blade views.

            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
