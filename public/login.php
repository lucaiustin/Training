<?php
    require_once('../common.php');

    $error = [];
    $error['username'] = '';
    $error['password'] = '';
    $error['submit'] = '';
    $error['login'] = '';

    $validatedUsername = '';
    $validatedPassword = '';

    if (isset($_POST['submit'])) {
        $submitOk = True;

        if (strlen($_POST['username']) > 3) {
            $validatedUsername = validateInput($_POST['username']);
        } else {
            $error['username'] = translate('Username error!');
            $submitOk = False;
        }

        if (strlen($_POST['password']) > 3) {
            $validatedPassword = validateInput($_POST['password']);
        } else {
            $error['password'] = translate('Password error!');
            $submitOk = False;
        }

        if ($submitOk == True) {
            if (strcmp($validatedUsername, USERNAME) == 0 && strcmp($validatedPassword, PASSWORD) == 0) {
                $_SESSION['username'] = $validatedUsername;
                header('Location: products.php');
                exit;
            } else {
                $error['login'] = translate('Invalid user or password.');
            }
        } else {
            $error['submit'] = translate('Please try again.');
        }
    }
?>
<html>
    <head>
        <title><?= translate('Login'); ?></title>
    </head>
    <body>
        <form action="login.php" method="post">
            <input type="text" name="username" value="<?= $validatedUsername; ?>" placeholder="<?= translate('Username'); ?>"><br>
            <?= $error['username']; ?>

            <input type="password" name="password" value="<?= $validatedPassword; ?>" placeholder="<?= translate('Password'); ?>"><br>
            <?= $error['password']; ?>

            <input type="submit" name="submit" placeholder="<?= translate('Login'); ?>">
            <?= $error['submit']; ?>
            <?= $error['login']; ?>
        </form>
    </body>
</html>
