<?php
    require_once ("common.php");

    session_start();
    //$_SESSION['cart'] = [];
    if (!isset($_SESSION["cart"])) {
        $_SESSION['cart'] = [];
    }

    $session_cart_ids = $_SESSION['cart'];

    $stmt = $dbh->prepare('SELECT * FROM products WHERE id NOT IN ('.implode(", ",$session_cart_ids).')');
    $stmt->execute( array(':table_name' => $table_name) );

    if ($stmt !== FALSE) {
        $products = $stmt->fetchAll();
    } else {
        $products = [];
    }

    if (isset($_GET["id"])) {
        $id = htmlspecialchars($_GET["id"]);
        if (!in_array($id, $_SESSION['cart'])) {
            array_push($_SESSION['cart'], htmlspecialchars($_GET["id"]));
        }
        header('Location: index.php');
    }
?>

<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
    <body>
        <div class = "container">
            <div class = "product-list">
                <?php foreach($products as $product): ?>
                    <div class = "product">
                        <div class = "product-image">
                             <img src = 'images/<?= $product['image_name']; ?>'>
                        </div>
                        <div class = "product-info">
                            <?= $product['id']; ?>
                            <?= translate($product['title']); ?>
                        </div>
                        <a href = '/index.php?id=<?= $product['id']; ?>'>Add</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href = ''>Go to cart</a>
        </div>
    </body>
</html>
