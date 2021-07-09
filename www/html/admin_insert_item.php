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

// DBからログインユーザーのデータを取得
$user = get_login_user($db);

// ログインユーザーが管理者でなければ
if(is_admin($user) === false){
  // ログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// 'name'のPOST値を取得
$name = get_post('name');
// 'price'のPOST値を取得
$price = get_post('price');
// 'status'のPOST値を取得
$status = get_post('status');
// 'stock'のPOST値を取得
$stock = get_post('stock');

// アップロードされた'image'の値を取得
$image = get_file('image');

// 商品が正常に登録されたら
if(regist_item($db, $name, $price, $stock, $status, $image)){
  // 結果のメッセージをセット
  set_message('商品を登録しました。');
// 条件外なら
}else {
  // エラーメッセージをセット
  set_error('商品の登録に失敗しました。');
}


// 管理ページにリダイレクト
redirect_to(ADMIN_URL);