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

if (isset( $_GET['id'] )) {
    $stmt = $dbh->prepare( 'SELECT * FROM orders WHERE id = ?' );
    $stmt->execute( [$_GET['id']] );
    $order = $stmt->fetch();

    $stmt = $dbh->prepare( 'SELECT * FROM products p JOIN products_orders po ON p.id = po.product_id WHERE po.order_id = ?' );
    $stmt->execute( [$_GET['id']] );
    $products = $stmt->fetchAll();
    $order['products'] = $products;

    $order['total'] = 0;
    foreach ($products as $product) {
        $order['total'] += $product['price'];
    }
}
?>
<html>
<head>
    <title><?= translate( 'Order' ); ?></title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="order">
        <?= $order['name']; ?>
        <br>
        <?= $order['customer_details']; ?>
        <br>
        <?= $order['comments']; ?>
        <br>
        <?= $order['total']; ?>
        <br>
        <?= $order['creation_date']; ?>
        <br><br>

        <?php if (count( $order['products'] ) > 0): ?>
            <?php foreach ($order['products'] as $product): ?>
                <div class="product">
                    <div class="product-image">
                        <img src="images/<?= $product['image_name']; ?>">
                    </div>
                    <div class="product-info">
                        <p><?= $product["id"]; ?></p>
                        <p><?= $product["title"]; ?></p>
                        <p><?= $product["description"]; ?></p>
                        <p><?= $product["price"]; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
