<?php
    require_once('../common.php');

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $errors = [];
    $errors['customer_details'] = '';
    $errors['products'] = '';
    $submit_message = '';
    if (isset($_POST['submit'])) {
        $submit_message = 'Please try again.';
         if (strlen($_POST['customer-details']) > 3) {
             if (count($_SESSION['cart']) > 0) {
                 $customer_details = validateInput($_POST['customer-details']);
                 $date = date('Y-m-d H:i:s');

                 $stmt = $dbh->prepare('INSERT INTO orders (customer_details, creation_date) VALUES (?,?)');
                 $stmt->execute([$customer_details, $date]);
                 $last_order_id= $dbh->lastInsertId();
                 foreach ($_SESSION['cart'] as $product_id) {
                     $stmt = $dbh->prepare('INSERT INTO products_orders (product_id, order_id) VALUES (?,?)');
                     $stmt->execute([$product_id, $last_order_id]);
                 }
                 $submit_message = 'The order has been created.';
             } else {
                 $errors['products'] = 'There are not enough products!';
             }
         } else {
             $errors['customer_details'] = 'Input customer details error!';
         }
    }

    $orders = [];
    $stmt = $dbh->prepare('SELECT * FROM orders');
    $stmt->execute();
    $orders_info = $stmt->fetchAll();

    foreach ($orders_info as $info) {
        $order = [];
        $order['id'] = $info['id'];
        $order['creation_date'] = $info['creation_date'];
        $order['customer_details'] = $info['customer_details'];

        $stmt = $dbh->prepare('SELECT * FROM products p JOIN products_orders po ON p.id = po.product_id WHERE po.order_id = ?');
        $stmt->execute([$info['id']]);
        $products = $stmt->fetchAll();
        $order['products'] = $products;

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
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="customer-details" placeholder="<?= translate('Customer details') ?>">
        <?= translate($errors['customer_details']); ?>
        <?= translate($errors['products']); ?>
        <input type="submit" name="submit" value="<?= translate('Create New Order'); ?>">
        <?= translate($submit_message); ?>
    </form>
        <div class="container">
            <div class="orders">
                <?php foreach($orders as $order):?>
                    <div class="order">
                        <?= $order['creation_date']; ?>
                        <?= $order['customer_details']; ?>
                        <?= $order['total']; ?>
                        <ul>
                            <?php foreach($order['products'] as $product): ?>
                                <li><
                                    <?= $product['id']; ?>
                                    <?= $product['title']; ?>
                                    <?= $product['description']; ?>
                                    <?= $product['price']; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <a href = "order.php?id=<?= $order['id']; ?>">View Order</a>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>
