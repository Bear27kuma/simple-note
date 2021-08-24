function deleteHandle(event) {
    // イベント（フォーム）の動作を一旦止めることができる
    event.preventDefault();
    if(window.confirm('本当に削除していいですか？')) {
        // 削除OKならイベント（フォーム）の動きを再開
        document.getElementById('delete-form').submit();
    } else {
        alert('キャンセルしました');
    }
}