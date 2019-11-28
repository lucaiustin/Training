<?php
    require_once('../common.php');

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $errors = [];
    $errors['customer_details'] = '';
    $errors['products'] = '';
    $submit_message = '';

    $orders = [];
    $stmt = $dbh->prepare('SELECT * FROM orders');
    $stmt->execute();
    $orders_info = $stmt->fetchAll();

    foreach ($orders_info as $info) {
        $order = [];
        $order['id'] = $info['id'];
        $order['creation_date'] = $info['creation_date'];
        $order['customer_details'] = $info['customer_details'];

        $stmt = $dbh->prepare('SELECT sum(p.price) FROM products p JOIN products_orders po ON p.id = po.product_id WHERE po.order_id = ? GROUP BY  po.order_id');
        $stmt->execute([$info['id']]);
        $order['total'] = $stmt->fetch()[0];

        array_push($orders, $order);
    }
?>
<html>
    <head>
        <title><?= translate('Orders'); ?></title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <div class="container">
            <div class="orders">
                <?php foreach($orders as $order):?>
                    <div class="order">
                        <?= $order['creation_date']; ?>
                        <?= $order['customer_details']; ?>
                        <?= $order['total']; ?>
                    </div>
                    <a href = "order.php?id=<?= $order['id']; ?>">View Order</a>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>
