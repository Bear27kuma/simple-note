<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            // unsignedで符号（+, -）をつけない、基本的にid周りのカラムにはunsignedをつける
            // 第2引数にtrueが入っているのは自動でインクリメントさせるため
            $table->unsignedBigInteger('id', true);
            // ノートの内容（長いテキスト）
            $table->longText('content');
            $table->unsignedBigInteger('user_id');
            // 論理削除を定義 → deleted_atを自動生成（DBにデータは残っているので、削除された時間を入れておくことで復元可能になる）
            $table->softDeletes();
            // timestampと書いてしまうと、レコード挿入時、更新時に値が入らないので、DB::rawで直接SQL文を書いている
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            // 外部キー制約、user_idに入る値はusersテーブルのidに存在するもののみという制約
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
