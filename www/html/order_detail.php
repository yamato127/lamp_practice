<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// orderデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'order.php';

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

// 'order_id'のGET値を取得
$order_id = get_get('order_id');

// 指定の注文番号の購入履歴を取得
$order = get_order($db, $order_id, $user['user_id'], is_admin($user));

// 購入履歴が取得できていなければ
if($order === false){
  // エラーメッセージを表示してスクリプトを終了
  exit(h("エラーが発生しました"));
}

// 購入明細を取得
$order_details = get_order_details($db, $order_id);

// ビューの読み込み
include_once VIEW_PATH . 'order_detail_view.php';