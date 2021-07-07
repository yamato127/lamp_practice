<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';
// cartデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'cart.php';

// セッションを開始
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

// 'cart_id'のPOST値を取得
$cart_id = get_post('cart_id');

// カートの商品データが正常に削除されたら
if(delete_cart($db, $cart_id)){
  // 結果のメッセージをセット
  set_message('カートを削除しました。');
// 条件外なら
} else {
  // エラーメッセージをセット
  set_error('カートの削除に失敗しました。');
}

// カートページにリダイレクト
redirect_to(CART_URL);