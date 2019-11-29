<?php
    require_once('../common.php');

    $order = [];
    $order['customer_details'] = '';
    $order['creation_date'] = '';
    $order['products'] = '';
    $order['total'] = '';
    $order['products'] = [];

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
                <?php if(count($order['products']) > 0): ?>
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
                <?php endif; ?>
                <?= $order['total']; ?>
            </div>
        </div>
    </body>
</html>
