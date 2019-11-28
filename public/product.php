<?php
    require_once('../common.php');

    $product = [];
    $product['title'] = '';
    $product['description'] = '';
    $product['price'] = '';
    $product['image_name'] = '';

    if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $stmt = $dbh->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $product = $stmt->fetch();
        if (!$product) {
            header('Location: products.php');
            exit;
        }
    } else {
        $product['title'] = 'Title';
        $product['description'] = 'Description';
        $product['price'] = 'Price';
        $product['image_name'] = 'Image';
    }

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

            $tmp = explode('.', $file_name);
            $file_ext = strtolower(end($tmp));
            $extensions = array('jpeg', 'jpg', 'png');

            if (in_array( $file_ext, $extensions ) === false) {
                $errors['image_file'] = 'extension not allowed, please choose a JPEG or PNG file.';
                $submit_ok = False;
            }

            if ($file_size > 2097152) {
                $errors['image_file'] = 'File size must be excately 2 MB';
                $submit_ok = False;
            }
        }

        if (strlen($_POST['title']) > 5) {
            $title = validateInput($_POST['title']);
        } else {
            $submit_ok = False;
            $errors['title'] = 'Input title error!';
        }

        if (strlen($_POST['description']) > 5) {
            $description = validateInput($_POST['description']);
        } else {
            $submit_ok = False;
            $errors['description'] = 'Input description error!';
        }

        if (strlen($_POST['price']) > 3) {
            $price = validateInput($_POST['price']);
        } else {
            $submit_ok = False;
            $errors['price'] = 'Input price error!';
        }

        if (strlen($_POST['title']) > 5) {
            $image_name = validateInput($file_name);
        } else {
            $submit_ok = False;
            $errors['image_name'] = 'Input image_name error!';
        }

        if ($submit_ok) {
            move_uploaded_file($file_tmp, 'images/' . $file_name);

            if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
                $stmt = $dbh->prepare('UPDATE products SET title=?, description=?, price=?, image_name=? WHERE id=?');
                $stmt->execute([$title, $description, $price, $image_name, $_GET['id']]);
            } else {
                $stmt = $dbh->prepare('INSERT INTO products (title, description, price, image_name) VALUES (?,?,?,?)');
                $stmt->execute([$title, $description, $price, $image_name]);

                header('Location: products.php');
                exit;
            }
        } else {
            $errors['submit'] = 'Submit error!';
        }
    }
?>
<html>
    <head>
        <title><?= translate('Product') ?></title>
    </head>
    <body>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="title" value="<?= $product['title']; ?>">
            <?= translate($errors['title']); ?>
            <br>
            <input type="text" name="description" value="<?= $product['description']; ?>">
            <?= translate($errors['contact_details']); ?>
            <br>
            <input type="text" name="price" value="<?= $product['price']; ?>">
            <?= translate($errors['price']); ?>
            <br>
            <input type="text" name="image-name" value="<?= $product['image_name']; ?>">
            <?= translate($errors['image_name']); ?>
            <br>
            <input type="file" name="image">
            <?= translate($errors['image_file']); ?>
            <br>
            <input type="submit" name="submit" value="<?= translate('Save'); ?>">
        </form>
        <?= $errors['submit']; ?>
        <a href="products.php"><?= translate('Products'); ?></a>
    </body>
</html>
