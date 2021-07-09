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
// ログインユーザーのデータを取得
$user = get_login_user($db);

// 'item_id'のPOST値を取得
$item_id = get_post('item_id');

// 商品を正常にカートに追加できたら
if(add_cart($db,$user['user_id'], $item_id)){
  // 結果のメッセージをセット
  set_message('カートに商品を追加しました。');
// 条件外なら
} else {
  // エラーメッセージをセット
  set_error('カートの更新に失敗しました。');
}

// ホームページにリダイレクト
redirect_to(HOME_URL);