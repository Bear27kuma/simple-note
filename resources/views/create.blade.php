@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">新規ノート作成</div>
        {{--route('store')と記述すると、自動的に/storeに書き換えられる--}}
        <form class="card-body" action="{{ route('store') }}" method="POST">
            {{--なりすまし送信防止の対策として@csrfをつける（Cross Site Request Forgeries）--}}
            {{--他人がなりすましてログインし、データを送信する攻撃手法。そのためLaravelのformではcsrfトークンを発行する--}}
            @csrf
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3" placeholder="ここに内容を入力"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </div>
@endsection
