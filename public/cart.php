<?php
require_once('../common.php');

require_once('../vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;

if (!isset( $_SESSION['cart'] )) {
    $_SESSION['cart'] = [];
}

if (isset( $_GET['id'] )) {
    if (filter_var( $_GET['id'], FILTER_VALIDATE_INT )) {
        if (($key = array_search( $_GET['id'], $_SESSION['cart'] )) !== false) {
            unset( $_SESSION['cart'][$key] );
        }
    }
    header( 'Location: cart.php' );
    exit;
}

if (count( $_SESSION['cart'] ) > 0) {
    $questionMarksArray = array_fill( 0, count( $_SESSION['cart'] ), '?' );
    $questionMarksString = implode( ", ", $questionMarksArray );
    $stmt = $dbh->prepare( 'SELECT * FROM products WHERE id IN (' . $questionMarksString . ')' );
    $stmt->execute( $_SESSION['cart'] );
    $products = $stmt->fetchAll();
} else {
    $products = [];
}

$submitOk = True;
$errors = [];
$errors['name'] = '';
$errors['contact_details'] = '';
$errors['comments'] = '';
$errors['send_email'] = '';
$mailStatus = '';
$saveOrderStatus = '';
$orderStatus = '';
$submitMessage = '';

$name = '';
$contactDetails = '';
$comments = '';

// Validate form data and send email
if (isset( $_POST['submit'] )) {
    if (strlen( $_POST['name'] ) > 5) {
        $name = validateInput( $_POST['name'] );
    } else {
        $submitOk = False;
        $errors['name'] = translate( 'Input name error!' );
    }

    if (strlen( $_POST['contact_details'] ) > 5) {
        $contactDetails = validateInput( $_POST['contact_details'] );
    } else {
        $submitOk = False;
        $errors['contact_details'] = translate( 'Input contact error!' );
    }

    if (strlen( $_POST['comments'] ) > 5) {
        $comments = validateInput( $_POST['comments'] );
    } else {
        $submitOk = False;
        $errors['comments'] = translate( 'Input comments error!' );
    }

    if (count( $_SESSION['cart'] ) < 1) {
        $submitOk = False;
        $orderStatus = translate( 'There are not enough products!' );
    }

    if ($submitOk) {
        //Save data to database

        $date = date( 'Y-m-d H:i:s' );

        $stmt = $dbh->prepare( 'INSERT INTO orders (name, customer_details, creation_date, comments) VALUES (?,?,?,?)' );
        $stmt->execute( [$name, $contactDetails, $date, $comments] );
        $lastOrderId = $dbh->lastInsertId();

        foreach ($_SESSION['cart'] as $productId) {
            $stmt = $dbh->prepare( 'INSERT INTO products_orders (product_id, order_id) VALUES (?,?)' );
            $stmt->execute( [$productId, $lastOrderId] );
        }
        $orderStatus = translate( 'The order has been created.' );

        //Send email
        //Compose a simple HTML email message
        $message = '<html><body>';
        $message .= translate( 'Name' ) . ': ' . $name . ' ' . translate( 'Contact Details' ) . ': ' . $contactDetails . ' ' . translate( 'Comments' ) . ': ' . $comments;
        $message .= '<div class="product-list">';
        foreach ($products as $product) {
            $imageSrc = (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/images/' . $product['image_name'];
            $message .= '<div class="product">';
            $message .= '<img src="' . $imageSrc . '">';
            $message .= $product['id'] . translate( 'Title' ) . ': ' . $product['title'] . ' ' . translate( 'Price' ) . ': ' . $product['price'];
            $message .= '</div>';
        }
        $message .= '</div>';
        $message .= '</body></html>';


        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 2525;

        $mail->setFrom( USER_EMAIL );
        $mail->AddAddress( SHOP_MANAGER_EMAIL );
        $mail->Subject = translate( 'New order' );

        $mail->isHTML( true );

        $mail->Body = $message;

        // Sending email
        if ($mail->send()) {
            $mailStatus = translate( 'Your mail has been sent successfully.' );
        } else {
            $mailStatus = translate( 'Unable to send email. Error' ) . ': ' . $mail->ErrorInfo;
        }
    } else {
        $submitMessage = translate( 'Please try again.' );
    }
}
?>
<html>
<head>
    <title><?= translate( 'Cart' ); ?></title>
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
                    <p><?= $product['id']; ?></p>
                    <p><?= $product['title']; ?></p>
                    <p><?= $product['description']; ?></p>
                    <p><?= $product['price']; ?></p>
                </div>
            </div>
            <a href="/cart.php?id=<?= $product['id']; ?>"><?= translate( 'Remove' ); ?></a>
            <hr>
        <?php endforeach; ?>
    </div>
    <a href="index.php"><?= translate( 'Go to index' ); ?></a>
    <form method="post">
        <input type="text" name="name" value="<?= $name; ?>" placeholder="<?= translate( 'Name' ); ?>">
        <?= $errors['name']; ?>
        <br>
        <input type="text" name="contact_details" value="<?= $contactDetails; ?>"
               placeholder="<?= translate( 'Contact Details' ); ?>">
        <?= $errors['contact_details']; ?>
        <br>
        <textarea name="comments" rows="10" cols="30"
                  placeholder="<?= translate( 'Comments' ) ?>"><?= $comments; ?></textarea>
        <?= $errors['comments']; ?>
        <br>
        <button name="submit" type="submit"><?= translate( 'Checkout' ) ?></button>
    </form>
    <?= $submitMessage; ?>
    <?= $saveOrderStatus; ?>
    <?= $mailStatus; ?>
    <?= $orderStatus; ?>
</div>
</body>
</html>
