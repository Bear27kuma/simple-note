@extends('layouts.app')

{{--javascriptという名前でセクションを作り、レイアウトファイルで読み込む--}}
@section('javascript')
    <script src="/js/confirm.js"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            ノート編集
            <form class="card-body" id="delete-form" action="{{ route('destroy') }}" method="POST">
                @csrf
                {{--削除機能も同様にどのノートを削除するのかをidで示すためinputのhiddenを設置--}}
                <input type="hidden" name="note_id" value="{{ $edit_note[0]['id'] }}">
                <button type="submit" onclick="deleteHandle(event);">削除</button>
            </form>
        </div>
        <form class="card-body" action="{{ route('update') }}" method="POST">
            @csrf
            {{--どのノートを編集しているかを示すため、inputのhiddenでノートのidを埋め込んでおく → コントローラー側でどのノートをupdateさせるかが理解できる--}}
            <input type="hidden" name="note_id" value="{{ $edit_note[0]['id'] }}">
            <div class="form-group">
                <textarea class="form-control" name="content" rows="3" placeholder="ここに内容を入力">{{ $edit_note[0]['content'] }}</textarea>
            </div>
            @error('content')
                <div class="alert alert-danger">ノート内容を入力してください</div>
            @enderror
            @foreach($tags as $tag)
                <div class="form-check form-check-inline mb-3">
                    {{--三項演算子で紐づいているタグだけチェックを入れる処理を記述する--}}
                    {{--もし$include_tagsにループで回っているタグのidが含まれれば、checkedをつける--}}
                    <input class="form-check-input" type="checkbox" name="tags[]" id="{{ $tag['id'] }}" value="{{ $tag['id'] }}" {{ in_array($tag['id'], $include_tags) ? "checked" : "" }}
                    <label class="form-check-label" for="{{ $tag['id'] }}">{{ $tag['name'] }}</label>
                </div>
            @endforeach
            <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="新しいタグを入力">
            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
@endsection
