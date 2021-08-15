<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

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

        // テーブルのカラム名と一致させる
        Note::insert(['content' => $posts['content'], 'user_id' => \Auth::id()]);

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
}
