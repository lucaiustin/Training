<?php
    require_once('../common.php');

    if (isset($_GET['logout'])) {
        unset($_SESSION['username']);
        header('Location: products.php');
        exit;
    }

    if (isset($_SESSION['username'])) {
        $stmt = $dbh->prepare('SELECT * FROM products');
        $stmt->execute();

        $products = $stmt->fetchAll();
    } else {
        header('Location: login.php');
        exit;
    }

    if (isset($_GET['delete']) && filter_var($_GET['delete'], FILTER_VALIDATE_INT)) {
        $stmt = $dbh->prepare('DELETE FROM products WHERE id =:id');
        $stmt->bindParam(':id', $_GET['delete']);
        $stmt->execute();
        header('Location: products.php');
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
                            <img src="images/<?= $product['image_name']; ?>">
                        </div>
                        <div class="product-info">
                            <?= $product['id']; ?>
                            <?= $product['title']; ?>
                            <?= $product['price']; ?>
                        </div>
                        <a href="product.php?id=<?= $product['id']; ?>"><?= translate('Edit'); ?></a>
                        <a href="products.php?delete=<?= $product['id']; ?>"><?= translate('Delete'); ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="product.php"><?= translate('Add'); ?></a>
            <a href="products.php?logout=True"><?= translate('Logout'); ?></a>
        </div>
    </body>
</html>
