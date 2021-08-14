<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoteTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // notesテーブルとtagsテーブルをつなぐ中間テーブルの役割
        Schema::create('note_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('note_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('note_id')->references('id')->on('notes');
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('note_tags');
    }
}
