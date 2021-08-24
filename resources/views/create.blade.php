@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">新規ノート作成</div>
        {{--route('store')と記述すると、自動的に/storeに書き換えられる--}}
        <form class="card-body my-card-body" action="{{ route('store') }}" method="POST">
            {{--なりすまし送信防止の対策として@csrfをつける（Cross Site Request Forgeries）--}}
            {{--他人がなりすましてログインし、データを送信する攻撃手法。そのためLaravelのformではcsrfトークンを発行する--}}
            @csrf
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3" placeholder="ここに内容を入力"></textarea>
            </div>
            @error('content')
                <div class="alert alert-danger">ノート内容を入力してください</div>
            @enderror
            {{--foreachでDBから取得したタグを一覧表示する--}}
            @foreach($tags as $tag)
                <div class="form-check form-check-inline mb-3">
                    {{--nameがtags[]と配列になっているのは、ループ処理で複数のタグが設定されることを想定して、配列形式で送信する--}}
                    <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $tag['id'] }}" value="{{ $tag['id'] }}">
                    <label class="form-check-label" for="{{ $tag['id'] }}">{{ $tag['name'] }}</label>
                </div>
            @endforeach
            <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="新しいタグを入力">
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
@endsection
