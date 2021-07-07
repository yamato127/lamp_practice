<?php 
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// DBに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// DBからカート内の全商品データを取得する関数
function get_user_carts($db, $user_id){
  // SQL文を作成
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
  ";
  // SQL文を実行して取得した結果を返す
  return fetch_all_query($db, $sql);
}

// DBからカート内の指定の$item_idの商品データを取得する関数
function get_user_cart($db, $user_id, $item_id){
  // SQL文を作成
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
    AND
      items.item_id = {$item_id}
  ";

  // SQL文を実行して取得した結果を返す
  return fetch_query($db, $sql);

}

// カートに商品を追加する関数
function add_cart($db, $user_id, $item_id ) {
  // DBからカート内の$item_idの商品データを取得
  $cart = get_user_cart($db, $user_id, $item_id);
  // カート内に商品がなければ
  if($cart === false){
    // カートに新しく商品を追加
    return insert_cart($db, $user_id, $item_id);
  }
  // カート内の商品数量を1増加
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

// カートに新しく商品を追加する関数
function insert_cart($db, $user_id, $item_id, $amount = 1){
  // SQL文を作成
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES({$item_id}, {$user_id}, {$amount})
  ";
  // SQL文実行の成否を返す
  return execute_query($db, $sql);
}

// カート内の商品数量を変更する関数
function update_cart_amount($db, $cart_id, $amount){
  // SQL文を作成
  $sql = "
    UPDATE
      carts
    SET
      amount = {$amount}
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";
  // SQL文実行の成否を返す
  return execute_query($db, $sql);
}

// カート内の商品データを削除する関数
function delete_cart($db, $cart_id){
  // SQL文を作成
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";

  // SQL文実行の成否を返す
  return execute_query($db, $sql);
}

// カート内の商品の購入処理を行う関数
function purchase_carts($db, $carts){
  // カート内の商品が購入できない状態であれば
  if(validate_cart_purchase($carts) === false){
    // falseを返す
    return false;
  }
  // カート内の商品データを順次参照
  foreach($carts as $cart){
    // 商品の在庫数を正常に変更できなければ
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      // エラーメッセージをセット
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  // カート内の全商品データを削除
  delete_user_carts($db, $carts[0]['user_id']);
}

// カート内の全商品データを削除する関数
function delete_user_carts($db, $user_id){
  // SQL文を作成
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = {$user_id}
  ";

  // SQL文を実行
  execute_query($db, $sql);
}

// カート内商品の合計金額を取得する関数
function sum_carts($carts){
  // 合計金額の初期化
  $total_price = 0;
  // カート内の商品データを順次参照
  foreach($carts as $cart){
    // 商品毎の小計金額を合計金額に足す
    $total_price += $cart['price'] * $cart['amount'];
  }
  // 合計金額を返す
  return $total_price;
}

// カート内の商品が正常に購入できるか検証する関数
function validate_cart_purchase($carts){
  // カート内の商品数が0であれば
  if(count($carts) === 0){
    // エラーメッセージをセット
    set_error('カートに商品が入っていません。');
    // falseを返す
    return false;
  }
  // カート内の商品データを順次参照
  foreach($carts as $cart){
    // 商品が非公開になっていれば
    if(is_open($cart) === false){
      // エラーメッセージをセット
      set_error($cart['name'] . 'は現在購入できません。');
    }
    // 商品の購入数量より在庫数が少なければ
    if($cart['stock'] - $cart['amount'] < 0){
      // エラーメッセージをセット
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  // エラーメッセージがあれば
  if(has_error() === true){
    // falseを返す
    return false;
  }
  // trueを返す
  return true;
}

