<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';

// セッション開始
session_start();

// CSRFトークンが不正なら
if(valid_csrf_token() !== true) {
  // エラーメッセージを表示してスクリプトを終了
  exit(h('エラーが発生しました'));
}

// ログインしていなければ
if(is_logined() === false){
  // ログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// DBに接続（PDOを取得）
$db = get_db_connect();

// DBからログインユーザーのデータを取得
$user = get_login_user($db);

// ログインユーザーが管理者でなければ
if(is_admin($user) === false){
  // ログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// 'item_id'のPOST値を取得
$item_id = get_post('item_id');


// 商品データが正常に削除されたら
if(destroy_item($db, $item_id) === true){
  // 結果のメッセージをセット
  set_message('商品を削除しました。');
// 条件外なら
} else {

  // エラーメッセージをセット
  set_error('商品削除に失敗しました。');
}



// 管理ページにリダイレクト
redirect_to(ADMIN_URL);