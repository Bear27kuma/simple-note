<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    public function getMyNote() {
        $query_tag = \Request::query('tag');
        // ==== ベースのメソッド ====
        $query = Note::select('notes.*')
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC');    // ASC=昇順、DESC=降順
        // ==== ベースのメソッドここまで ====

        // もしクエリパラメータにtagがあれば
        if(!empty($query_tag)) {
            // tagのidで絞り込み
            // ここで、ノートを取得（whereNullで削除されていないものだけ取得）
            $query->leftJoin('note_tags', 'note_tags.note_id', '=', 'notes.id')->where('note_tags.tag_id', '=', $query_tag);
        }

        $notes = $query->get();
        return $notes;
    }
}
