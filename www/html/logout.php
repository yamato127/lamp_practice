<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';

// セッション開始
session_start();
// セッション変数を削除
$_SESSION = array();
// セッションCookieのパラメータを取得
$params = session_get_cookie_params();
// セッションCookieを削除
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
// セッションIDを無効化
session_destroy();

// ログインページにリダイレクト
redirect_to(LOGIN_URL);

