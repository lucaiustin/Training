<?php
    require_once('../common.php');

    checkLogin();

    if (isset($_GET['logout'])) {
        unset($_SESSION['username']);
        header('Location: login.php');
        exit;
    }

    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        //Delete the image from folder
        $stmt = $dbh->prepare('SELECT image_name FROM products WHERE id = :id');
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        $imageName = $stmt->fetch()['image_name'];
        unlink('images/' . $imageName);

        //Delete row from database
        $stmt = $dbh->prepare('DELETE FROM products WHERE id = :id');
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();

        header('Location: products.php');
        exit;
    }

    $stmt = $dbh->prepare('SELECT * FROM products');
    $stmt->execute();

    $products = $stmt->fetchAll();
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
                            <p><?= $product['id']; ?></p>
                            <p><?= $product['title']; ?></p>
                            <p><?= $product['description']; ?></p>
                            <p><?= $product['price']; ?></p>
                        </div>
                    </div>
                    <a href="product.php?id=<?= $product['id']; ?>"><?= translate('Edit'); ?></a>
                    <a href="products.php?id=<?= $product['id']; ?>"><?= translate('Delete'); ?></a>
                    <hr>
                <?php endforeach; ?>
            </div>
            <a href="product.php"><?= translate('Add'); ?></a>
            <a href="products.php?logout=True"><?= translate('Logout'); ?></a>
        </div>
    </body>
</html>
