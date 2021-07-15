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

// CSRFトークンの生成
$csrf_token = get_csrf_token();

// ログインしていなければ
if(is_logined() === false){
  // ログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// 'selected_sort'のGET値を取得
$selected_sort = get_get('selected_sort');

// DBに接続（PDOを取得）
$db = get_db_connect();
// ログインユーザーのデータを取得
$user = get_login_user($db);

// 公開している商品データを取得
$items = get_open_items($db, $selected_sort);

// ビューを読み込み
include_once VIEW_PATH . 'index_view.php';