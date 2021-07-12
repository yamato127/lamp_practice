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
// orderデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'order.php';

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

// カート内の全商品データを取得
$carts = get_user_carts($db, $user['user_id']);

// 商品が正常に購入できなければ
if(purchase_carts($db, $user['user_id'], $carts) === false){
  // エラーメッセージをセット
  set_error('商品が購入できませんでした。');
  // カートページにリダイレクト
  redirect_to(CART_URL);
} 

// カート内商品の合計金額を取得
$total_price = sum_carts($carts);

// ビューの読み込み
include_once '../view/finish_view.php';