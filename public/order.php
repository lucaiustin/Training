<?php
    require_once('../common.php');

    $order = [];
    if (isset($_GET['id'])) {
        $stmt = $dbh->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $order = $stmt->fetch();

        $stmt = $dbh->prepare('SELECT * FROM products p JOIN products_orders po ON p.id = po.product_id WHERE po.order_id = ?');
        $stmt->execute([$_GET['id']]);
        $products = $stmt->fetchAll();
        $order['products'] = $products;

        $stmt = $dbh->prepare('SELECT sum(p.price) FROM products p JOIN products_orders po ON p.id = po.product_id WHERE po.order_id = ? GROUP BY po.order_id');
        $stmt->execute([$_GET['id']]);
        $order['total'] = $stmt->fetch()[0];
    }
?>
<html>
    <head>
        <title>
            <?= translate('Order'); ?>
        </title>
    </head>
    <body>
        <div class="container">
            <div class="order">
                <?= $order['customer_details']; ?>
                <br>
                <?= $order['creation_date']; ?>
                <ul>
                    <?php foreach($order['products'] as $product): ?>
                        <li>
                            <?= $product['id']; ?>
                            <?= $product['title']; ?>
                            <?= $product['description']; ?>
                            <?= $product['price']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?= $order['total']; ?>
            </div>
        </div>
    </body>
</html>
