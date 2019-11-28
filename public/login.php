<?php
    require_once('../common.php');

    $error = [];
    $error['username'] = '';
    $error['password'] = '';
    $error['submit'] = '';

    if (isset($_POST['submit'])) {
        $submit_ok = True;

        if (strlen($_POST['username']) > 3) {
            $validated_username = validateInput($_POST['username']);
        } else {
            $error['username'] = 'Username error!';
            $submit_ok = False;
        }

        if (strlen($_POST['password']) > 3) {
            $validated_password = validateInput($_POST['password']);
        } else {
            $error['password'] = 'Password error!';
            $submit_ok = False;
        }

        if ($submit_ok == True) {
            if (strcmp($validated_username, USERNAME) == 0 && strcmp($validated_password, PASSWORD) == 0) {
                $_SESSION['username'] = $validated_username;
                header('Location: products.php');
                exit;
            }
        } else {
            $error['submit'] = 'Please try again.';
        }
    }
?>
<html>
    <head>
        <title><?= translate('Login'); ?></title>
    </head>
    <body>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="<?= translate('Username'); ?>"><br>
            <?= $error['username']; ?>

            <input type="password" name="password" placeholder="<?= translate('Password'); ?>"><br>
            <?= $error['password']; ?>

            <input type="submit" name="submit" placeholder="<?= translate('Login'); ?>">
            <?= $error['submit']; ?>
        </form>
    </body>
</html>
