<?php
// クリックジャッキング対策
header("X-FRAME-OPTIONS: DENY");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'order_detail.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <div class="container">
    <h1>購入明細</h1>

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <ul class="order">
      <li>注文番号：<?php print(h($order['order_id'])); ?></li>
      <li>購入日時：<?php print(h($order['order_date'])); ?></li>
      <li>合計金額：<?php print(h(number_format($order['total_price']))); ?>円</li>
    </ul>
    <?php if(count($order_details) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($order_details as $order_detail){ ?>
          <tr>
            <td><?php print(h($order_detail['name'])); ?></td>
            <td><?php print(h(number_format($order_detail['order_price']))); ?>円</td>
            <td><?php print(h($order_detail['amount'])); ?></td>
            <td><?php print(h(number_format($order_detail['subtotal_price']))); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>購入明細がありません。</p>
    <?php } ?> 
  </div>
</body>
</html>