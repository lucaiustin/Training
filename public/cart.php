<?php

    require_once('../common.php');

    session_start();

    $question_marks_array = array_fill(0, count($_SESSION['cart']), '?');
    $question_marks_string = implode(", ", $question_marks_array);
    $stmt = $dbh->prepare('SELECT * FROM products WHERE id IN ('.$question_marks_string.')');
    foreach ($_SESSION['cart'] as $k => $id) {
        $stmt->bindValue(($k+1), $id);
    }
    $stmt->execute();

    if ($stmt !== FALSE) {
        $products = $stmt->fetchAll();
    } else {
        $products = [];
    }

    if (isset($_GET['id'])) {
        if (filter_var($_GET['id'], FILTER_VALIDATE_INT) && in_array($id, $_SESSION['cart'])) {
            if (($key = array_search($_GET['id'], $_SESSION['cart'])) !== false) {
                unset($_SESSION['cart'][$key]);
            }
        }
        header('Location: cart.php');
        exit;
    }

    $send_email = True;
    $error_name = '';
    $error_contact_details = '';
    $error_comments = '';
    $mail_status = '';
    // Validate form data and send email
    if (isset($_POST['submit'])) {
        if (strlen($_POST['name']) > 5) {
            $name = validateInput($_POST['name']);
        } else {
            $send_email = False;
            $error_name = 'Input name error!';
        }

        if (strlen($_POST['contact_details']) > 5) {
            $contact_details = validateInput($_POST['contact_details']);
        } else {
            $send_email = False;
            $error_contact_details = 'Input contact error!';
        }

        if (strlen($_POST['comments']) > 5) {
            $comments = validateInput($_POST['comments']);
        } else {
            $send_email = False;
            $error_comments = 'Input comments error';
        }

        if ($_SESSION['cart'] < 1) {
            $send_email = False;
        }

        if ($send_email) {
            $to = $shop_manager_email;
            $subject = 'New order';
            $from = 'peterparker@email.com';

            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Create email headers
            $headers .= 'From: ' . $from . "\r\n".
                'Reply-To: ' . $from . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            // Compose a simple HTML email message
            $message = '<html><body>';
            $message .= 'Name: ' . $name . 'Contact Details: ' . $contact_details . 'Comments: ' . $comments;
            $message .= '<div class="product-list">';
            foreach ($products as $product) {
                $message .= '<div class="product">';
                $message .= '<img src="images/<?= $product["image_name"]; ?>">';
                $message .= $product["id"] . $product["title"] . $product["price"];
                $message .= '</div>';
            }
            $message .= '</div>';
            $message .= '</body></html>';

            // Sending email
            if (mail($to, $subject, $message, $headers)) {
                $mail_status = 'Your mail has been sent successfully.';
            } else {
                $mail_status = 'Unable to send email. Please try again.';
            }
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
            <form action="/cart.php" method="post">
                <input type="text" name="name" value="<?= translate('Name'); ?>">
                <?= $error_name ?>
                <br>
                <input type="text" name="contact_details" value="<?= translate('Contact details'); ?>">
                <?= $error_contact_details ?>
                <br>
                <textarea name="comments" rows="10" cols="30"><?= translate('Comments'); ?></textarea>
                <?= $error_comments ?>
                <br>
                <input type="submit" name="submit" value="<?= translate('Checkout'); ?>">
            </form>
                <?= $mail_status; ?>
        </div>
    </body>
</html>