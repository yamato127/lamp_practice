<?php
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// DBに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// DBからユーザーID指定でユーザーデータを取得する関数
function get_user($db, $user_id){
  // SQL文を作成
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = {$user_id}
    LIMIT 1
  ";

  // SQL文を実行した結果を返す
  return fetch_query($db, $sql);
}

// DBからユーザー名指定でユーザーデータを取得する関数
function get_user_by_name($db, $name){
  // SQL文を作成
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = '{$name}'
    LIMIT 1
  ";

  // SQL文を実行した結果を返す
  return fetch_query($db, $sql);
}

// 指定のユーザーでログイン状態にする関数
function login_as($db, $name, $password){
  // ユーザーデータを取得
  $user = get_user_by_name($db, $name);
  // ユーザーデータが取得できていない、またはパスワードが間違っていれば
  if($user === false || $user['password'] !== $password){
    // falseを返す
    return false;
  }
  // セッション変数に'user_id'を保存
  set_session('user_id', $user['user_id']);
  // ユーザーデータを返す
  return $user;
}

// DBからログインユーザーのユーザーデータを取得する関数
function get_login_user($db){
  // セッション変数に保存されているユーザーIDを取得
  $login_user_id = get_session('user_id');

  // DBから取得したユーザーデータを返す
  return get_user($db, $login_user_id);
}

// ユーザー登録を行う関数
function regist_user($db, $name, $password, $password_confirmation) {
  // 入力値が正しくなければ
  if( is_valid_user($name, $password, $password_confirmation) === false){
    // falseを返す
    return false;
  }
  
  // DBにユーザーデータを追加し結果の成否を返す
  return insert_user($db, $name, $password);
}

// ユーザーが管理者であるかチェックする関数
function is_admin($user){
  // 管理者であればtrueを返す
  return $user['type'] === USER_TYPE_ADMIN;
}

// ユーザー登録時の入力値の整合性をチェックする関数
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  // ユーザー名の整合性チェック
  $is_valid_user_name = is_valid_user_name($name);
  // パスワードの整合性チェック
  $is_valid_password = is_valid_password($password, $password_confirmation);
  // すべての整合性が取れていればtrueを返す
  return $is_valid_user_name && $is_valid_password ;
}

// ユーザー名の整合性をチェックする関数
function is_valid_user_name($name) {
  // 結果用変数の初期化
  $is_valid = true;
  // ユーザー名の文字数が指定の範囲外ならば
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    // エラーメッセージをセット
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // ユーザー名に半角英数字以外の文字が入っていれば
  if(is_alphanumeric($name) === false){
    // エラーメッセージをセット
    set_error('ユーザー名は半角英数字で入力してください。');
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // 整合性チェックの結果を返す
  return $is_valid;
}

// パスワードの整合性をチェックする関数
function is_valid_password($password, $password_confirmation){
  // 結果用変数の初期化
  $is_valid = true;
  // パスワードの文字数が指定の範囲外ならば
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    // エラーメッセージをセット
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // パスワードに半角英数字以外の文字が入っていれば
  if(is_alphanumeric($password) === false){
    // エラーメッセージをセット
    set_error('パスワードは半角英数字で入力してください。');
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // 再確認用のパスワードと一致しなければ
  if($password !== $password_confirmation){
    // エラーメッセージをセット
    set_error('パスワードがパスワード(確認用)と一致しません。');
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // 整合性チェックの結果を返す
  return $is_valid;
}

// DBにユーザーデータを追加する関数
function insert_user($db, $name, $password){
  // SQL文を作成
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES ('{$name}', '{$password}');
  ";

  // SQL文実行の成否を返す
  return execute_query($db, $sql);
}

