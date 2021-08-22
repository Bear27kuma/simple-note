<?php

namespace App\Providers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 全てのメソッドが呼ばれる前に先に呼ばれるメソッド
        view()->composer('*', function ($view) {
            $query_tag = \Request::query('tag');
            // もしクエリパラメータにtagがあれば
            if(!empty($query_tag)) {
                // tagのidで絞り込み
                // ここで、ノートを取得（whereNullで削除されていないものだけ取得）
                $notes = Note::select('notes.*')
                    ->leftJoin('note_tags', 'note_tags.note_id', '=', 'notes.id')
                    ->where('note_tags.tag_id', '=', $query_tag)
                    ->where('user_id', '=', \Auth::id())
                    ->whereNull('deleted_at')
                    ->orderBy('updated_at', 'DESC')    // ASC=昇順、DESC=降順
                    ->get();
            } else {
                // tagがなければ全て取得する
                $notes = Note::select('notes.*')
                    ->where('user_id', '=', \Auth::id())
                    ->whereNull('deleted_at')
                    ->orderBy('updated_at', 'DESC')
                    ->get();
            }

            $tags = Tag::where('user_id', '=', \Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('id', 'DESC')
                ->get();

            // すべてのViewに$notesを渡す
            $view->with('notes', $notes)->with('tags', $tags);
        });
    }
}
