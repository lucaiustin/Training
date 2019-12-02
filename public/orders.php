<?php
    require_once('../common.php');

    checkLogin();

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $errors = [];
    $errors['customer_details'] = '';
    $errors['products'] = '';
    $submitMessage = '';

    $orders = [];
    $stmt = $dbh->prepare('SELECT o.id, creation_date,  customer_details, sum(p.price) as price_sum FROM orders o INNER JOIN products_orders po on o.id = po.order_id INNER JOIN products p ON po.product_id = p.id GROUP BY o.id');
    $stmt->execute();
    $ordersInfo = $stmt->fetchAll();

    foreach ($ordersInfo as $info) {
        $order = [];
        $order['id'] = $info['id'];
        $order['creation_date'] = $info['creation_date'];
        $order['customer_details'] = $info['customer_details'];
        $order['total'] = $info['price_sum'];
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
                    <a href = "order.php?id=<?= $order['id']; ?>"><?= translate('View Order'); ?></a>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>
