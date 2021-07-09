<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';

// セッション開始
session_start();

// CSRFトークンの生成
$csrf_token = get_csrf_token();

// 既にログインしていれば
if(is_logined() === true){
  // ホームページにリダイレクト
  redirect_to(HOME_URL);
}

// ビューを読み込み
include_once VIEW_PATH . 'signup_view.php';



