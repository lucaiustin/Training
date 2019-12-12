<?php
require_once('../common.php');

if (!isset( $_SESSION['cart'] )) {
    $_SESSION['cart'] = [];
}

if (isset( $_GET['id'] )) {
    if (filter_var( $_GET['id'], FILTER_VALIDATE_INT ) && !in_array( $_GET['id'], $_SESSION['cart'] )) {
        array_push( $_SESSION['cart'], $_GET['id'] );
    }
    header( 'Location: index.php' );
    exit;
}

if (count( $_SESSION['cart'] ) > 0) {
    $questionMarksArray = array_fill( 0, count( $_SESSION['cart'] ), '?' );
    $questionMarksString = implode( ', ', $questionMarksArray );
    $stmt = $dbh->prepare( 'SELECT * FROM products WHERE id NOT IN (' . $questionMarksString . ')' );
} else {
    $stmt = $dbh->prepare( 'SELECT * FROM products' );
}
$stmt->execute( $_SESSION['cart'] );

if ($stmt !== FALSE) {
    $products = $stmt->fetchAll();
} else {
    $products = [];
}
?>
<html>
<head>
    <title><?= translate( 'Products' ); ?></title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="product-list">
        <?php foreach ($products as $product): ?>
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
            <a href="/index.php?id=<?= $product["id"]; ?>"><?= translate( 'Add' ); ?></a>
            <hr>
        <?php endforeach; ?>
    </div>
    <a href="cart.php"><?= translate( 'Go to cart' ); ?></a>
</div>
</body>
</html>
