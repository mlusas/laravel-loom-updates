<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loom_urls', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('file_path');
            $table->integer('line_number')->nullable();
            $table->date('date')->nullable();
            $table->string('author')->nullable();
            $table->string('title')->nullable();
            $table->text('image_url')->nullable();
            $table->string('tag')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loom_urls');
    }
};
