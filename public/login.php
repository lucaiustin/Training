<?php
    require_once('../common.php');

    $error = [];
    $error['username'] = '';
    $error['password'] = '';
    $error['submit'] = '';

    $validatedUsername = '';
    $validatedPassword = '';

    if (isset($_POST['submit'])) {
        $submitOk = True;

        if (strlen($_POST['username']) > 3) {
            $validatedUsername = validateInput($_POST['username']);
        } else {
            $error['username'] = translate('username is invalid');
            $submitOk = False;
        }

        if (strlen($_POST['password']) > 3) {
            $validatedPassword = validateInput($_POST['password']);
        } else {
            $error['password'] = translate('password is invalid');
            $submitOk = False;
        }

        if ($submitOk == True) {
            $loginFlag = True;
            if (strcmp($validatedUsername, USERNAME) != 0) {
                $error['username'] = translate('username is invalid');
                $loginFlag = False;
            }

            if (strcmp($validatedPassword, PASSWORD) != 0) {
                $error['password'] = translate('password is invalid');
                $loginFlag = False;
            }

            if ($loginFlag == True) {
                $_SESSION['username'] = $validatedUsername;
                header('Location: products.php');
                exit;
            }
        } else {
            $error['submit'] = translate('Please try again.');
        }
    }
?>
<html>
    <head>
        <title><?= translate('Login'); ?></title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
    </head>
    <body>
        <div class="container">
            <form action="login.php" method="post">
                <input type="text" name="username" value="<?= $validatedUsername; ?>" placeholder="<?= translate('Username'); ?>">
                <?= $error['username']; ?>
                <br>
                <input type="password" name="password" value="<?= $validatedPassword; ?>" placeholder="<?= translate('Password'); ?>">
                <?= $error['password']; ?>
                <br>
                <button type="submit" name="submit"><?= translate('Login') ?></button>
                <?= $error['submit']; ?>
            </form>
        </div>
    </body>
</html>
