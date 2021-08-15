@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">ノート編集</div>
        <form class="card-body" action="{{ route('store') }}" method="POST">
            @csrf
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3" placeholder="ここに内容を入力">{{ $edit_note['content'] }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
@endsection
