<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';

// セッション開始
session_start();

// CSRFトークンが不正なら
if(valid_csrf_token() !== true) {
  // エラーメッセージを表示してスクリプトを終了
  exit(h('エラーが発生しました'));
}

// 既にログインしていれば
if(is_logined() === true){
  // ホームページにリダイレクト
  redirect_to(HOME_URL);
}

// 'name'のPOST値を取得
$name = get_post('name');
// 'password'のPOST値を取得
$password = get_post('password');

// DBに接続（PDOを取得）
$db = get_db_connect();


// DBから名前とパスワードが一致するユーザーデータを取得し、ログイン状態にする
$user = login_as($db, $name, $password);
// 一致するユーザーがなければ
if( $user === false){
  set_error('ログインに失敗しました。');
  // エラーメッセージをセット
  redirect_to(LOGIN_URL);
}

// 結果のメッセージをセット
set_message('ログインしました。');
// ログインユーザーが管理者なら
if ($user['type'] === USER_TYPE_ADMIN){
  // 管理ページにリダイレクト
  redirect_to(ADMIN_URL);
}
// ホームページにリダイレクト
redirect_to(HOME_URL);