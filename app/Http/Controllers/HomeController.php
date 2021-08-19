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
        // ここで、ノートを取得（whereNullで削除されていないものだけ取得）
        $notes = Note::select('notes.*')
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')    // ASC=昇順、DESC=降順
            ->get();

        // compactメソッドに変数名を指定すると、Viewに値を渡せる
        return view('create', compact('notes'));
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
        });
        // ==== ここまでがトランザクションの範囲 ====

        // テーブルのカラム名と一致させる
        // Note::insert(['content' => $posts['content'], 'user_id' => \Auth::id()]);

        // /homeにリダイレクトする
        return redirect( route('home') );
    }

    public function edit($id)
    {
        $notes = Note::select('notes.*')
            ->where('user_id', '=', \Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'DESC')
            ->get();

        // 一意なidをfindで一つ取得する
        $edit_note = Note::find($id);

        return view('edit', compact('notes', 'edit_note'));
    }

    public function update(Request $request)
    {
        $posts = $request->all();

        // updateでは必ずwhereをつけて、どのnote_idがupdateされるかをDBに示してあげる
        Note::where('id', $posts['note_id'])->update(['content' => $posts['content']]);

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
