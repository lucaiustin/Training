<?php

    require_once('../common.php');

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_GET['id'])) {
        if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
            if (($key = array_search($_GET['id'], $_SESSION['cart'])) !== false) {
                unset($_SESSION['cart'][$key]);
            }
        }
        header('Location: cart.php');
        exit;
    }

    if (count($_SESSION['cart']) > 0) {
        $question_marks_array = array_fill(0, count($_SESSION['cart']), '?');
        $question_marks_string = implode(", ", $question_marks_array);
        $stmt = $dbh->prepare('SELECT * FROM products WHERE id IN ('.$question_marks_string.')');
        $stmt->execute($_SESSION['cart']);
        $products = $stmt->fetchAll();
    } else {
        $products = [];
    }

    $submit_ok = True;
    $errors = [];
    $errors['name'] = '';
    $errors['contact_details'] = '';
    $errors['comments'] = '';
    $errors['send_email'] = '';
    $mail_status = '';
    $save_order_status = '';

    $name = '';
    $contact_details = '';
    $comments = '';

    // Validate form data and send email
    if (isset($_POST['submit'])) {
        if (strlen($_POST['name']) > 5) {
            $name = validateInput($_POST['name']);
        } else {
            $submit_ok = False;
            $errors['name'] = 'Input name error!';
        }

        if (strlen($_POST['contact_details']) > 5) {
            $contact_details = validateInput($_POST['contact_details']);
        } else {
            $submit_ok = False;
            $errors['contact_details'] = 'Input contact error!';
        }

        if (strlen($_POST['comments']) > 5) {
            $comments = validateInput($_POST['comments']);
        } else {
            $submit_ok = False;
            $errors['comments'] = 'Input comments error';
        }

        if (count($_SESSION['cart']) < 1) {
            $submit_ok = False;
            $order_status = 'There are not enough products!';
        }

        if ($submit_ok) {
            //Save data to database
            $save_order_status = 'Please try again.';

            $date = date('Y-m-d H:i:s');

            $stmt = $dbh->prepare('INSERT INTO orders (customer_details, creation_date) VALUES (?,?)');
            $stmt->execute([$contact_details, $date]);
            $last_order_id = $dbh->lastInsertId();

            foreach ($_SESSION['cart'] as $product_id) {
                $stmt = $dbh->prepare('INSERT INTO products_orders (product_id, order_id) VALUES (?,?)');
                $stmt->execute([$product_id, $last_order_id]);
            }
            $save_order_status = 'The order has been created.';

            //Send email
            $to = SHOP_MANAGER_EMAIL;
            $subject = translate('New order');
            $from = SHOP_MANAGER_EMAIL;

            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

            // Create email headers
            $headers .= 'From: ' . $from . "\r\n".
                'Reply-To: ' . $from . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            // Compose a simple HTML email message
            $message = '<html><body>';
            $message .= 'Name: ' . translate($name) . ' Contact Details: ' . translate($contact_details) . ' Comments: ' . translate($comments);
            $message .= '<div class="product-list">';
            foreach ($products as $product) {
                $message .= '<div class="product">';
                $message .= '<img src="images/<?= $product["image_name"]; ?>">';
                $message .= $product["id"] . 'Title: ' . $product["title"] . ' Price: ' . $product["price"];
                $message .= '</div>';
            }
            $message .= '</div>';
            $message .= '</body></html>';

            // Sending email
            if (mail($to, $subject, $message, $headers)) {
                $mail_status = translate('Your mail has been sent successfully.');
            } else {
                $mail_status = translate('Unable to send email. Please try again.');
            }
        } else {
            $mail_status = translate('Unable to send email. Please try again.');
        }
    }
?>
<html>
    <head>
        <title><?= translate('Cart'); ?></title>
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
                            <?= $product["price"]; ?>
                        </div>
                        <a href="/cart.php?id=<?= $product["id"]; ?>"><?= translate('Remove');?></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="index.php"><?= translate('Go to index'); ?></a>
            <form method="post">
                <input type="text" name="name" value="<?= $name; ?>" placeholder="<?= translate('Name'); ?>">
                <?= $errors['name']; ?>
                <br>
                <input type="text" name="contact_details" value="<?= $contact_details; ?>" placeholder="<?= translate('Contact details'); ?>">
                <?= $errors['contact_details']; ?>
                <br>
                <textarea name="comments" rows="10" cols="30" placeholder="Comments"><?= $comments; ?></textarea>
                <?= $errors['comments']; ?>
                <br>
                <input type="submit" name="submit" placeholder="<?= translate('Checkout'); ?>">
            </form>
            <?= $save_order_status; ?>
            <?= $mail_status; ?>
        </div>
    </body>
</html>
