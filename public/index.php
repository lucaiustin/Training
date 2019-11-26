<?php
    require_once("../common.php");

    session_start();
    if (!isset($_SESSION["cart"])) {
        $_SESSION['cart'] = [];
    }
    $session_cart_ids = $_SESSION['cart'];
    $question_marks_array = array_fill(0, count($session_cart_ids), '?');
    $question_marks_string = implode(", ", $question_marks_array);
    $stmt = $dbh->prepare('SELECT * FROM products WHERE id NOT IN ('.$question_marks_string.')');
    foreach ($session_cart_ids as $k => $id) {
        $stmt->bindValue(($k+1), $id);
    }
    $stmt->execute();

    if ($stmt !== FALSE) {
        $products = $stmt->fetchAll();
    } else {
        $products = [];
    }

    if (isset($_GET["id"])) {
        if (filter_var($_GET["id"], FILTER_VALIDATE_INT) && !in_array($id, $_SESSION['cart'])) {
                array_push($_SESSION['cart'], $_GET["id"]);
        }
        header('Location: index.php');
        exit;
    }
?>

<html>
    <head>
        <title><?= translate('Products'); ?></title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <div class="container">
            <div class="product-list">
                <?php foreach($products as $product): ?>
                    <div class="product">
                        <div class="product-image">
                             <img src="images/<?= $product["image_name"]; ?>">
                        </div>
                        <div class="product-info">
                            <?= $product["id"]; ?>
                            <?= $product["title"]; ?>
                        </div>
                        <a href="/index.php?id=<?= $product["id"]; ?>">Add</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href=''>Go to cart</a>
        </div>
    </body>
</html>
