<?php
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// DBに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// DB利用

// DBからカート内の全商品データを取得する関数
function get_order($db, $order_id, $user_id, $admin){
  // SQL文を作成
  $sql = "
    SELECT
      orders.order_id,
      orders.user_id,
      orders.created as order_date,
      sum(order_details.order_price * order_details.amount) as total_price
    FROM
      orders
    JOIN
      order_details
    ON
      orders.order_id = order_details.order_id
    WHERE
      orders.order_id = ?
  ";
  // 管理者でなければユーザーIDを条件に追加
  if($admin === false){
    $sql .= "
      AND
        orders.user_id = ?
    ";
  }
  $sql .= "
    GROUP BY
      orders.order_id
  ";
  
   // プレースホルダにバインドする値の配列
   $params = array($order_id);
   // 管理者でなければユーザーIDを追加
   if($admin === false){
     $params[] = $user_id;
   }
   // SQL文を実行して取得した結果を返す
   return fetch_query($db, $sql, $params);
}

// DBからカート内の全商品データを取得する関数
function get_orders($db, $user_id, $admin){
  // SQL文を作成
  $sql = "
    SELECT
      orders.order_id,
      orders.user_id,
      orders.created as order_date,
      sum(order_details.order_price * order_details.amount) as total_price
    FROM
      orders
    JOIN
      order_details
    ON
      orders.order_id = order_details.order_id
  ";
  // 管理者でなければユーザーIDを条件に追加
  if($admin === false){
    $sql .= "
      WHERE
        orders.user_id = ?
    ";
  }
  $sql .= "
    GROUP BY
      orders.order_id
    ORDER BY
      order_date DESC
  ";
  // プレースホルダにバインドする値の配列
  $params = array();
  // 管理者でなければユーザーIDを追加
  if($admin === false){
    $params[] = $user_id;
  }
  // SQL文を実行して取得した結果を返す
  return fetch_all_query($db, $sql, $params);
}

function get_order_details($db, $order_id){
  // SQL文を作成
  $sql = "
    SELECT
      items.name,
      order_details.order_price,
      order_details.amount,
      order_details.order_price * order_details.amount as subtotal_price
    FROM
      order_details
    JOIN
      items
    ON
      order_details.item_id = items.item_id
    WHERE
      order_id = ?
  ";
  
  // プレースホルダにバインドする値の配列
  $params = array($order_id);
  // SQL文を実行して取得した結果を返す
  return fetch_all_query($db, $sql, $params);
}


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