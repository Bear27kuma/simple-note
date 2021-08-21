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
            // ここで、ノートを取得（whereNullで削除されていないものだけ取得）
            $notes = Note::select('notes.*')
                ->where('user_id', '=', \Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('updated_at', 'DESC')    // ASC=昇順、DESC=降順
                ->get();

            $tags = Tag::where('user_id', '=', \Auth::id())
                ->whereNull('deleted_at')
                ->orderBy('id', 'DESC')
                ->get();

            // すべてのViewに$notesを渡す
            $view->with('notes', $notes)->with('tags', $tags);
        });
    }
}
