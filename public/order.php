<?php
    require_once('../common.php');

    checkLogin();

    $order = [];
    $order['customer_details'] = '';
    $order['creation_date'] = '';
    $order['products'] = '';
    $order['comments'] = '';
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

        $order['total'] = 0;
        foreach($products as $product) {
            $order['total'] +=  $product['price'];
        }
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
                <?= $order['comments']; ?>
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
