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

// 'cart_id'のPOST値を取得
$cart_id = get_post('cart_id');
// 'amount'のPOST値を取得
$amount = get_post('amount');

// 商品の購入数が正常に変更されたら
if(update_cart_amount($db, $cart_id, $amount)){
  // 結果のメッセージをセット
  set_message('購入数を更新しました。');
// 条件外なら
} else {
  // エラーメッセージをセット
  set_error('購入数の更新に失敗しました。');
}

// カートページにリダイレクト
redirect_to(CART_URL);