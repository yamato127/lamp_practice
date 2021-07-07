<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';

// セッションを開始
session_start();

// ログインしていなれば
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

// DBから全商品のデータを取得
$items = get_all_items($db);
// ビューの読み込み
include_once VIEW_PATH . '/admin_view.php';
