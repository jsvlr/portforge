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
        // $this->down();
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->unique();
            $table->string('slug', 255)->unique();
            $table->string('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('status')->default(\App\Enums\PostStatusEnum::default());
            $table->date('published_at')->default(null)->nullable();
            $table->foreignId('post_category_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->integer('views')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
