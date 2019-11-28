<?php
    require_once('../common.php');

    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit;
    }

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

    if (isset($_POST['submit'])) {
        // Validate form inputs
        $submit_ok = True;

        if (isset($_FILES['image'])) {
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_type = $_FILES['image']['type'];

            if (strcmp($file_type, 'image/jpeg') != 0) {
                $errors['image_file'] = translate('extension not allowed, please choose a JPEG or PNG file.');
                $submit_ok = False;
            }

            if ($file_size > 2097152) {
                $errors['image_file'] = translate('File size must be exactly 2 MB');
                $submit_ok = False;
            }
        }

        if (strlen($_POST['title']) > 5) {
            $product['title'] = validateInput($_POST['title']);
        } else {
            $submit_ok = False;
            $errors['title'] = translate('Input title error!');
        }

        if (strlen($_POST['description']) > 5) {
            $product['description'] = validateInput($_POST['description']);
        } else {
            $submit_ok = False;
            $errors['contact_details'] = translate('Input description error!');
        }

        if (strlen($_POST['price']) > 3) {
            $product['price'] = validateInput($_POST['price']);
        } else {
            $submit_ok = False;
            $errors['price'] = translate('Input price error!');
        }

        if (strlen($file_name) > 5) {
            $image_name = validateInput($file_name);
            $product['image_name'] = $image_name;
        } else {
            $submit_ok = False;
            $errors['image_name'] = translate('Input image_name error!');
        }

        if ($submit_ok) {
            move_uploaded_file($file_tmp, 'images/' . $file_name);

            if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
                $stmt = $dbh->prepare('UPDATE products SET title=?, description=?, price=?, image_name=? WHERE id=?');
                $stmt->execute([$product['title'], $product['description'], $product['price'], $product['image_name'], $_GET['id']]);

                header('Location: products.php');
                exit;
            } else {
                $stmt = $dbh->prepare('INSERT INTO products (title, description, price, image_name) VALUES (?,?,?,?)');
                $stmt->execute([$product['title'], $product['description'], $product['price'], $product['image_name']]);

                header('Location: products.php');
                exit;
            }
        } else {
            $errors['submit'] = translate('Submit error!');
        }
    }

    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $stmt = $dbh->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $product = $stmt->fetch();
        if (!$product) {
            header('Location: products.php');
            exit;
        }
    }
?>
<html>
    <head>
        <title><?= translate('Product') ?></title>
    </head>
    <body>
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
            <input type="text" name="image-name" value="<?= $product['image_name']; ?>" placeholder="<?= translate('Image Name'); ?>">
            <?= $errors['image_name']; ?>
            <br>
            <input type="file" name="image">
            <?= $errors['image_file']; ?>
            <br>
            <input type="submit" name="submit" value="<?= translate('Save'); ?>">
        </form>
        <?= $errors['submit']; ?>
        <a href="products.php"><?= translate('Products'); ?></a>
    </body>
</html>
