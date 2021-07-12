<?php

// DBに購入履歴を追加する関数
function insert_order($db, $user_id){
  // SQL文を作成
  $sql = "
    INSERT INTO
      orders(
        user_id
      )
    VALUES(?)
  ";

  // プレースホルダにバインドする値の配列
  $params = array($user_id);
  // SQL文実行の成否を返す
  return execute_query($db, $sql, $params);
}

// DBに購入明細を追加する関数
function insert_order_detail($db, $order_id, $item_id, $price, $amount){
  // SQL文を作成
  $sql = "
    INSERT INTO
      order_details(
        order_id,
        item_id,
        order_price,
        amount
      )
    VALUES(?, ?, ?, ?)
  ";

  // プレースホルダにバインドする値の配列
  $params = array($order_id, $item_id, $price, $amount);
  // SQL文実行の成否を返す
  return execute_query($db, $sql, $params);
}