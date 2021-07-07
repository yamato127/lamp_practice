<?php
// DBに接続する関数
function get_db_connect(){
  // MySQL用のDSN文字列
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
 
  // 例外処理
  try {
    // データベースに接続
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    // エラーモードを設定（エラー発生時にPDOExceptionを投げる）
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // プリペアドステートメントを設定（エミュレーションを無効）
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // デフォルトのフェッチモードを設定（カラム名を添え字とする配列を返す）
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  // 例外が発生したなら
  } catch (PDOException $e) {
    // エラーメッセージを出力し処理を終了する
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  // PDOを返す
  return $dbh;
}

// SQL文を実行して取得した結果（1行）を返す関数
function fetch_query($db, $sql, $params = array()){
  // 例外処理
  try{
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQL文を実行
    $statement->execute($params);
    // レコードを取得
    return $statement->fetch();
  // 例外が発生したなら
  }catch(PDOException $e){
    // エラーメッセージをセット
    set_error('データ取得に失敗しました。');
  }
  // falseを返す
  return false;
}

// SQL文を実行して取得した結果（全行）を返す関数
function fetch_all_query($db, $sql, $params = array()){
  // 例外処理
  try{
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQL文を実行
    $statement->execute($params);
    // レコードを取得
    return $statement->fetchAll();
  // 例外が発生したなら
  }catch(PDOException $e){
    // エラーメッセージをセット
    set_error('データ取得に失敗しました。');
  }
  // falseを返す
  return false;
}

// SQL文を実行する関数
function execute_query($db, $sql, $params = array()){
  // 例外処理
  try{
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQL文を実行
    return $statement->execute($params);
  // 例外が発生したなら
  }catch(PDOException $e){
    // エラーメッセージをセット
    set_error('更新に失敗しました。');
  }
  // falseを返す
  return false;
}