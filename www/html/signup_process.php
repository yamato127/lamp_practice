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
// 'password_confirmation'のPOST値を取得
$password_confirmation = get_post('password_confirmation');

// DBに接続（PDOを取得）
$db = get_db_connect();

// 例外処理
try{
  $result = regist_user($db, $name, $password, $password_confirmation);
  // ユーザー登録が正常にできなかったら
  if( $result=== false){
    // エラーメッセージをセット
    set_error('ユーザー登録に失敗しました。');
    // サインアップページにリダイレクト
    redirect_to(SIGNUP_URL);
  }
// 例外が発生したなら
}catch(PDOException $e){
  // エラーメッセージをセット
  set_error('ユーザー登録に失敗しました。');
  // サインアップページにリダイレクト
  redirect_to(SIGNUP_URL);
}

// 結果のメッセージをセット
set_message('ユーザー登録が完了しました。');
// 登録したユーザーでログイン状態にする
login_as($db, $name, $password);
// ホームページにリダイレクト
redirect_to(HOME_URL);