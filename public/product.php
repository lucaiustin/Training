<?php
    require_once('../common.php');

    checkLogin();

    $product = [];
    $product['title'] = '';
    $product['description'] = '';
    $product['price'] = '';
    $product['image_name'] = '';

    $errors = [];
    $errors['title'] = '';
    $errors['contact_details'] = '';
    $errors['price'] = '';
    $errors['image_name'] = '';
    $errors['image_file'] = '';
    $errors['submit'] = '';

    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $stmt = $dbh->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $product = $stmt->fetch();
        if (!$product) {
            header('Location: products.php');
            exit;
        }
    }

    if (isset($_POST['submit'])) {
        // Validate form inputs
        $submitOk = True;

        if (isset($_FILES['image'])) {
            $fileName = uniqid();
            $fileSize = $_FILES['image']['size'];
            $fileTmp = $_FILES['image']['tmp_name'];
            $fileType = $_FILES['image']['type'];

            if (strcmp($fileType, 'image/jpeg') != 0) {
                $errors['image_file'] = translate('extension not allowed, please choose a JPEG or PNG file.');
                $submitOk = False;
            }

            if ($fileSize > 2097152) {
                $errors['image_file'] = translate('File size must be exactly 2 MB.');
                $submitOk = False;
            }
        }

        $product['title'] = validateInput($_POST['title']);
        $product['description'] = validateInput($_POST['description']);
        $product['price'] = validateInput($_POST['price']);

        if (strlen($product['title']) < 5) {
            $submitOk = False;
            $errors['title'] = translate('Input title error!');
        }

        if (strlen($product['description']) < 5) {
            $submitOk = False;
            $errors['contact_details'] = translate('Input description error!');
        }

        if (!is_numeric($product['price'])) {
            $submitOk = False;
            $errors['price'] = translate('Input price error!');
        }

        if ($submitOk) {
            unlink('images/' . $product['image_name']);
            move_uploaded_file($fileTmp, 'images/' . $fileName);

            if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
                $stmt = $dbh->prepare('UPDATE products SET title=?, description=?, price=?, image_name=? WHERE id=?');
                $stmt->execute([$product['title'], $product['description'], $product['price'], $fileName, $_GET['id']]);

                header('Location: products.php');
                exit;
            } else {
                $stmt = $dbh->prepare('INSERT INTO products (title, description, price, image_name) VALUES (?,?,?,?)');
                $stmt->execute([$product['title'], $product['description'], $product['price'], $fileName]);

                header('Location: products.php');
                exit;
            }
        } else {
            $errors['submit'] = translate('Submit error!');
        }
    }
?>
<html>
    <head>
        <title><?= translate('Product') ?></title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <div class="container">
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="title" value="<?= $product['title']; ?>" placeholder="<?= translate('Title'); ?>">
                <?= $errors['title']; ?>
                <br>
                <input type="text" name="description" value="<?= $product['description']; ?>" placeholder="<?= translate('Description'); ?>">
                <?= $errors['contact_details']; ?>
                <br>
                <input type="text" name="price" value="<?= $product['price']; ?>" placeholder="<?= translate('Price'); ?>">
                <?= $errors['price']; ?>
                <br>
                <input type="file" name="image">
                <?= $errors['image_file']; ?>
                <br>
                <input type="submit" name="submit" value="<?= translate('Save'); ?>">
            </form>
            <?= $errors['submit']; ?>
            <a href="products.php"><?= translate('Products'); ?></a>
        </div>
    </body>
</html>
