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
            // 自分のノート取得はNoteモデルに任せる
            // インスタンス化（他のファイルでモデルを使う場合）
            $note_model = new Note();
            //ノートの取得を行う
            $notes = $note_model->getMyNote();

            $tags = Tag::where('user_id', '=', \Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('id', 'DESC')
                ->get();

            // すべてのViewに$notesを渡す
            $view->with('notes', $notes)->with('tags', $tags);
        });
    }
}
