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

// ログインユーザーのデータを取得
$user = get_login_user($db);

// ログインユーザーが管理者でなければ
if(is_admin($user) === false){
  // ログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// 'item_id'のPOST値を取得
$item_id = get_post('item_id');
// 'stock'のPOST値を取得
$stock = get_post('stock');

// 商品の在庫数が正常に変更されたら
if(update_item_stock($db, $item_id, $stock)){
  // 結果のメッセージをセット
  set_message('在庫数を変更しました。');
// 条件外なら
} else {
  // エラーメッセージをセット
  set_error('在庫数の変更に失敗しました。');
}

// 管理ページにリダイレクト
redirect_to(ADMIN_URL);