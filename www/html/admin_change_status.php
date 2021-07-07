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

// ログインしていなければ
if(is_logined() === false){
  // ログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// DBに接続（PDOを取得）
$db = get_db_connect();

// ログインユーザーのデータを取得
$user = get_login_user($db);

// ログインユーザーが管理者でなければ
if(is_admin($user) === false){
  // ログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// 'item_id'のPOST値を取得
$item_id = get_post('item_id');
// 'changes_to'のPOST値を取得
$changes_to = get_post('changes_to');

// 公開ボタンが押されたら
if($changes_to === 'open'){
  // ステータスを公開に変更
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  // 結果のメッセージをセット
  set_message('ステータスを変更しました。');
// 非公開ボタンが押されたら
}else if($changes_to === 'close'){
  // ステータスを非公開に変更
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  // 結果のメッセージをセット
  set_message('ステータスを変更しました。');
// 条件外なら
}else {
  // エラーメッセージをセット
  set_error('不正なリクエストです。');
}


// 管理ページにリダイレクト
redirect_to(ADMIN_URL);