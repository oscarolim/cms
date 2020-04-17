<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('sitemap_id');
            $table->string('block_id');
            $table->string('block_tag');
            $table->longText('block_content');
            $table->json('block_settings')->nullable();
            $table->softDeletes();

            $table->foreign('sitemap_id')->references('id')->on('sitemap')->onDelete('cascade');
            $table->unique(['sitemap_id', 'block_id', 'block_tag']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents');
    }
}
