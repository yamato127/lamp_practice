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

// 購入履歴を取得
$orders = get_orders($db, $user['user_id'], is_admin($user));

// ビューの読み込み
include_once VIEW_PATH . 'order_view.php';