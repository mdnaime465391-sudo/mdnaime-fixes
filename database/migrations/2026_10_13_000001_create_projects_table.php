<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('categories');
            $table->string('summary')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('accent_color', 32)->nullable();
            $table->longText('background')->nullable();
            $table->longText('challenge')->nullable();
            $table->longText('design_approach')->nullable();
            $table->json('features')->nullable();
            $table->json('tech_stack')->nullable();
            $table->longText('solution')->nullable();
            $table->longText('result')->nullable();
            $table->string('live_preview_url')->nullable();
            $table->string('demo_credentials')->nullable();
            $table->text('demo_notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
