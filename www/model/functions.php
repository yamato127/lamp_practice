<?php

// 変数の情報を表示してスクリプトを終了する関数
function dd($var){
  // 変数に関する情報を表示
  var_dump($var);
  // 現在のスクリプトを終了
  exit();
}

// 指定のURLにリダイレクトする関数
function redirect_to($url){
  // $ureにリダイレクト
  header('Location: ' . $url);
  // 現在のスクリプトを終了
  exit;
}

// GET値を取得する関数
function get_get($name){
  // $nameのGET値が送信されていれば
  if(isset($_GET[$name]) === true){
    // GET値を返す
    return $_GET[$name];
  };
  // 空文字を返す
  return '';
}

// POST値を取得する関数
function get_post($name){
  // $nameのPOST値が送信されていれば
  if(isset($_POST[$name]) === true){
    // POST値を返す
    return $_POST[$name];
  };
  // 空文字を返す
  return '';
}

// アップロードされたファイルの値を取得する関数
function get_file($name){
  // ファイルがアップロードされていれば
  if(isset($_FILES[$name]) === true){
    // アップロードされた値を返す
    return $_FILES[$name];
  };
  // 空の配列を返す
  return array();
}

// セッションの値を取得する関数
function get_session($name){
  // $nameのセッションが存在すれば
  if(isset($_SESSION[$name]) === true){
    // セッションの値を返す
    return $_SESSION[$name];
  };
  // 空文字を返す
  return '';
}

// セッションを設定する関数
function set_session($name, $value){
  // セッション変数に$nameを保存
  $_SESSION[$name] = $value;
}

// エラーメッセージを追加する関数
function set_error($error){
  // セッション変数にエラーメッセージを追加
  $_SESSION['__errors'][] = $error;
}

// エラーメッセージを取得する関数
function get_errors(){
  // セッション変数に保存されているエラーメッセージを取得
  $errors = get_session('__errors');
  // エラーメッセージがなければ
  if($errors === ''){
    // 空の配列を返す
    return array();
  }
  // セッション変数のエラーメッセージを削除
  set_session('__errors',  array());
  // エラーメッセージを返す
  return $errors;
}

// エラーメッセージの有無を返す関数
function has_error(){
  // セッション変数'__errors'が存在し、エラーメッセージが一つ以上あればtrueを返す
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

// 処理結果のメッセージを追加する関数
function set_message($message){
  // セッション変数にメッセージを追加
  $_SESSION['__messages'][] = $message;
}

// 処理結果のメッセージを取得する関数
function get_messages(){
  // セッション変数に保存されているメッセージを取得
  $messages = get_session('__messages');
  // メッセージがなければ
  if($messages === ''){
    // 空の配列を返す
    return array();
  }
  // セッション変数のメッセージを削除
  set_session('__messages',  array());
  // 処理結果のメッセージを返す
  return $messages;
}

// ログイン済かどうかチェックする関数
function is_logined(){
  // セッション変数'user_id'が存在すればtrueを返す
  return get_session('user_id') !== '';
}

// 新しいファイル名を取得する関数
function get_upload_filename($file){
  // アップロードした画像ファイルが正しくなければ
  if(is_valid_upload_image($file) === false){
    // 空文字を返す
    return '';
  }
  // imagetype定数を取得
  $mimetype = exif_imagetype($file['tmp_name']);
  // ファイル形式を取得
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  // 一意のファイル名を作成して返す
  return get_random_string() . '.' . $ext;
}

// 指定した長さのランダムな文字列を取得
function get_random_string($length = 20){
  // ランダムな文字列を返す
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

// アップロードした画像ファイルを保存する関数
function save_image($image, $filename){
  // アップロードされたファイルを指定のディレクトリに移動して保存（成否を返す）
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

// 指定のディレイクトリから画像ファイルを削除する関数
function delete_image($filename){
  // 指定のディレクトリに画像ファイルが存在すれば
  if(file_exists(IMAGE_DIR . $filename) === true){
    // 画像ファイルを削除
    unlink(IMAGE_DIR . $filename);
    // trueを返す
    return true;
  }
  // falseを返す
  return false;
  
}



// 文字数をチェックする関数
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  // 文字数を取得
  $length = mb_strlen($string);
  // 文字数が指定の範囲内ならtrueを返す
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

// 文字列が半角英数字のみかチェックする関数
function is_alphanumeric($string){
  // 文字列が半角英数字のみならtrueを返す
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

// 文字列が0以上の整数かチェックする関数
function is_positive_integer($string){
  // 文字列が0以上の整数ならtrueを返す
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

// 文字列のフォーマットをチェックする関数
function is_valid_format($string, $format){
  // バリデーションの結果を返す
  return preg_match($format, $string) === 1;
}


// アップロードした画像ファイルの整合性をチェックする関数
function is_valid_upload_image($image){
  // HTTP POSTで画像ファイルがアップロードされていなければ
  if(is_uploaded_file($image['tmp_name']) === false){
    // エラーメッセージをセット
    set_error('ファイル形式が不正です。');
    // falseを返す
    return false;
  }
  // imagetype定数を取得
  $mimetype = exif_imagetype($image['tmp_name']);
  // ファイル形式がPERMITTED_IMAGE_TYPESになければ
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    // エラーメッセージをセット
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    // falseを返す
    return false;
  }
  // trueを返す
  return true;
}

// 文字列を特殊文字に変換する関数
function h($str){
  // htmlエスケープを施した値を返す
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

