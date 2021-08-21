<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Tag;
use App\Models\NoteTag;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $tags = Tag::where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderby('id', 'DESC')
            ->get();

        // compactメソッドに変数名を指定すると、Viewに値を渡せる
        return view('create', compact('tags'));
    }

    public function store(Request $request)
    {
        $posts = $request->all();
        // dump dieの略 → メソッドの引数に取った値を展開して止める → データ確認
        // dd(\Auth::id());

        // ==== ここからトランザクション開始 ====
        // クロージャーを使う
        DB::transaction(function() use($posts) {
            // メモIDをインサートして取得
            // insertGetIdではインサートしてそのidを返す
            $note_id = Note::insertGetId(['content' => $posts['content'], 'user_id' => \Auth::id()]);
            // 新規タグがすでにtagsテーブルに存在するのかチェック
            // where文は続けて書くことができ、その場合「かつ」の意味になる
            $tag_exists = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])->exists();
            // 新規タグが入力されているかチェック
            if ( !empty($posts['new_tag']) && !$tag_exists ) {
                // 新規タグが存在しなければ、tagsテーブルにインサート → IDを取得（中間テーブルにtag_idを入れるため）
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                // note_tagsにインサートして、ノートとタグを紐づける
                NoteTag::insert(['note_id' => $note_id, 'tag_id' => $tag_id]);
            }
            // 既存タグが紐づけられた場合 → note_tagsテーブルにインサート
            foreach($posts['tags'] as $tag) {
                NoteTag::insert(['note_id' => $note_id, 'tag_id' => $tag]);
            }
        });
        // ==== ここまでがトランザクションの範囲 ====

        // テーブルのカラム名と一致させる
        // Note::insert(['content' => $posts['content'], 'user_id' => \Auth::id()]);

        // /homeにリダイレクトする
        return redirect( route('home') );
    }

    public function edit($id)
    {
        // 条件に一致するデータを取得する
        $edit_note = Note::select('notes.*', 'tags.id AS tag_id')
            ->leftJoin('note_tags', 'note_tags.note_id', '=', 'notes.id')
            ->leftJoin('tags', 'note_tags.tag_id', '=', 'tags.id')
            ->where('notes.user_id', '=', \Auth::id())
            ->where('notes.id', '=', $id)
            ->whereNull('notes.deleted_at')
            ->get();

        // tagは複数存在する可能性があるので、配列に格納してからViewに渡す
        $include_tags = [];
        foreach($edit_note as $note) {
            $include_tags[] = $note['tag_id'];
        }

        $tags = Tag::where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderby('id', 'DESC')
            ->get();

        return view('edit', compact('edit_note', 'include_tags', 'tags'));
    }

    public function update(Request $request)
    {
        $posts = $request->all();

        // ==== ここからトランザクション開始 ====
        DB::transaction(function() use($posts) {
            // updateでは必ずwhereをつけて、どのnote_idがupdateされるかをDBに示してあげる
            Note::where('id', $posts['note_id'])->update(['content' => $posts['content']]);
            // 一旦ノートとタグの紐付けを削除 → 中間テーブルを一旦削除
            NoteTag::where('note_id', '=', $posts['note_id'])->delete();
            // 再度ノートとタグの紐付け
            foreach ($posts['tags'] as $tag) {
                NoteTag::insert(['note_id' => $posts['note_id'], 'tag_id' => $tag]);
            }
            // 新規タグがすでにtagsテーブルに存在するのかチェック
            // もし、新しいタグの入力があれば、インサートして紐づける
            $tag_exists = Tag::where('user_id', '=', \Auth::id())->where('name', '=', $posts['new_tag'])->exists();
            // 新規タグが入力されているかチェック
            if ( !empty($posts['new_tag']) && !$tag_exists ) {
                // 新規タグが存在しなければ、tagsテーブルにインサート → IDを取得（中間テーブルにtag_idを入れるため）
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                // note_tagsにインサートして、ノートとタグを紐づける
                NoteTag::insert(['note_id' => $posts['note_id'], 'tag_id' => $tag_id]);
            }
        });
        // ==== ここまでがトランザクションの範囲 ====

        return redirect( route('home') );
    }

    public function destroy(Request $request)
    {
        $posts = $request->all();

        // deleteで削除してしまうと物理削除=データごと全て消してしまい、DBに何も残らなくなるため復元ができなくなる
        // deleted_atカラムにタイムスタンプを入れることで、DBにデータは残っているけどページには表示されない=論理削除になる
        Note::where('id', $posts['note_id'])->update(['deleted_at' => date('Y-m-d H:i:s', time())]);

        return redirect( route('home') );
    }
}
