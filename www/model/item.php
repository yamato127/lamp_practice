<?php
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// DBに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// DB利用

// DBから$item_idの商品データを取得する関数
function get_item($db, $item_id){
  // SQL文を作成
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = {$item_id}
  ";

  // SQL文を実行して取得した結果を返す
  return fetch_query($db, $sql);
}

// DBから全商品データを取得する関数
function get_items($db, $is_open = false){
  // SQL文を作成
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  // 引数にtrueが指定されていれば
  if($is_open === true){
    // ステータスが公開の商品を条件に追加
    $sql .= '
      WHERE status = 1
    ';
  }

  // SQL文を実行して取得した結果を返す
  return fetch_all_query($db, $sql);
}

// DBから全商品データを取得する関数
function get_all_items($db){
  // DBから取得したデータを返す
  return get_items($db);
}

// DBから公開されている全商品データを取得する関数
function get_open_items($db){
  // DBから取得したデータを返す
  return get_items($db, true);
}

// 商品登録を行う関数
function regist_item($db, $name, $price, $stock, $status, $image){
  // 新しいファイル名を取得
  $filename = get_upload_filename($image);
  // 入力値が正しくなければ
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    // falseを返す
    return false;
  }
  // DBに商品データを追加し結果の成否を返す
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

// 商品登録を行う関数（トランザクション部分）
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  // トランザクション開始
  $db->beginTransaction();
  // DBに商品データを追加してディレクトリに画像ファイルを保存できたら
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    // コミット処理
    $db->commit();
    // trueを返す
    return true;
  }
  // ロールバック処理
  $db->rollback();
  // falseを返す
  return false;
  
}

// DBに商品データを追加する関数
function insert_item($db, $name, $price, $stock, $filename, $status){
  // 公開ステータスを数値で取得
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  // SQL文を作成
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES('{$name}', {$price}, {$stock}, '{$filename}', {$status_value});
  ";

  // SQL文実行の成否を返す
  return execute_query($db, $sql);
}

// 商品の公開ステータスを変更する関数
function update_item_status($db, $item_id, $status){
  // SQL文を作成
  $sql = "
    UPDATE
      items
    SET
      status = {$status}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  // SQL文実行の成否を返す
  return execute_query($db, $sql);
}

// 商品の在庫数を変更する関数
function update_item_stock($db, $item_id, $stock){
  // SQL文を作成
  $sql = "
    UPDATE
      items
    SET
      stock = {$stock}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  // SQL文実行の成否を返す
  return execute_query($db, $sql);
}

// 商品に関するデータを全て削除する関数
function destroy_item($db, $item_id){
  // 商品データを取得
  $item = get_item($db, $item_id);
  // 商品データが取得できなければ
  if($item === false){
    // falseを返す
    return false;
  }
  // トランザクション開始
  $db->beginTransaction();
  // DBの商品データを削除し、ディレクトリの画像ファイルを削除できたら
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    // コミット処理
    $db->commit();
    // trueを返す
    return true;
  }
  // ロールバック処理
  $db->rollback();
  // falseを返す
  return false;
}

// DBの商品データを削除する関数
function delete_item($db, $item_id){
  // SQL文を作成
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  // SQL文実行の成否を返す
  return execute_query($db, $sql);
}


// 非DB

// 商品が公開になっているチェックする関数
function is_open($item){
  // 公開ならtrueを返す
  return $item['status'] === 1;
}

// 商品登録時の入力値の整合性をチェックする関数
function validate_item($name, $price, $stock, $filename, $status){
  // 商品名の整合性チェック
  $is_valid_item_name = is_valid_item_name($name);
  // 価格の整合性チェック
  $is_valid_item_price = is_valid_item_price($price);
  // 在庫数の整合性チェック
  $is_valid_item_stock = is_valid_item_stock($stock);
  // 画像ファイル名の整合性チェック
  $is_valid_item_filename = is_valid_item_filename($filename);
  // 公開ステータスの整合性チェック
  $is_valid_item_status = is_valid_item_status($status);

  // すべての整合性が取れていればtrueを返す
  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

// 商品名の整合性をチェックする関数
function is_valid_item_name($name){
  // 結果用変数の初期化
  $is_valid = true;
  // 商品名の文字数が指定の範囲外ならば
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    // エラーメッセージをセット
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // 整合性チェックの結果を返す
  return $is_valid;
}

// 価格の整合性をチェックする関数
function is_valid_item_price($price){
  // 結果用変数の初期化
  $is_valid = true;
  // 価格が0以上の整数でなければ
  if(is_positive_integer($price) === false){
    // エラーメッセージをセット
    set_error('価格は0以上の整数で入力してください。');
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // 整合性チェックの結果を返す
  return $is_valid;
}

// 在庫数の整合性をチェックする関数
function is_valid_item_stock($stock){
  // 結果用変数の初期化
  $is_valid = true;
  // 在庫数が0以上の整数でなければ
  if(is_positive_integer($stock) === false){
    // エラーメッセージをセット
    set_error('在庫数は0以上の整数で入力してください。');
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // 整合性チェックの結果を返す
  return $is_valid;
}

// 画像ファイル名の整合性をチェックする関数
function is_valid_item_filename($filename){
  // 結果用変数の初期化
  $is_valid = true;
  // 画像ファイル名が空文字なら
  if($filename === ''){
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // 整合性チェックの結果を返す
  return $is_valid;
}

// 公開ステータスの整合性をチェックする関数
function is_valid_item_status($status){
  // 結果用変数の初期化
  $is_valid = true;
  // 公開・非公開以外の値が入力されていたら
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    // 結果用変数をfalseに変更
    $is_valid = false;
  }
  // 整合性チェックの結果を返す
  return $is_valid;
}