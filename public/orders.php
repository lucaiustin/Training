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

    $stmt = $dbh->prepare('SELECT o.id, creation_date,  customer_details, sum(p.price) as price_sum FROM orders o INNER JOIN products_orders po on o.id = po.order_id INNER JOIN products p ON po.product_id = p.id GROUP BY o.id');
    $stmt->execute();
    $orders = $stmt->fetchAll();
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
                        <p><?= $order['creation_date']; ?></p>
                        <p><?= $order['customer_details']; ?></p>
                        <p><?= $order['price_sum']; ?></p>
                    </div>
                    <a href = "order.php?id=<?= $order['id']; ?>"><?= translate('View Order'); ?></a>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>
