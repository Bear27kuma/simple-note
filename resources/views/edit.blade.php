@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">ノート編集</div>
        <form class="card-body" action="{{ route('update') }}" method="POST">
            @csrf
            {{--どのノートを編集しているかを示すため、inputのhiddenでノートのidを埋め込んでおく → コントローラー側でどのノートをupdateさせるかが理解できる--}}
            <input type="hidden" name="note_id" value="{{ $edit_note['id'] }}">
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3" placeholder="ここに内容を入力">{{ $edit_note['content'] }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
@endsection