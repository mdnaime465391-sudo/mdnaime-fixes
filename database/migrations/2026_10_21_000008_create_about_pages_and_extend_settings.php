<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->default('ABOUT');
            $table->string('headline');
            $table->string('intro', 500)->nullable();
            $table->string('portrait_path')->nullable();

            $table->string('story_heading')->nullable();
            $table->longText('story')->nullable();
            $table->json('principles')->nullable();
            $table->json('toolkit')->nullable();

            $table->string('availability_label')->nullable();

            $table->boolean('show_story')->default(true);
            $table->boolean('show_principles')->default(true);
            $table->boolean('show_toolkit')->default(true);
            $table->boolean('show_testimonials')->default(true);
            $table->boolean('show_cta')->default(true);

            $table->string('seo_title')->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->timestamps();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('availability_label')->nullable()->after('tagline');
            $table->string('location')->nullable()->after('availability_label');
            $table->string('og_image_path')->nullable()->after('favicon_path');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['availability_label', 'location', 'og_image_path']);
        });

        Schema::dropIfExists('about_pages');
    }
};
