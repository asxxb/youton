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
        Schema::create('generated_shorts', function (Blueprint $table) {
               $table->id();
        $table->string('prompt');
        $table->string('status')->default('processing');
        $table->text('script')->nullable();
        $table->string('preview_image')->nullable();
        $table->string('video_path')->nullable();
        $table->text('error')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_shorts');
    }
};
