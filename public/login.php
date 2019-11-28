<?php
    require_once('../common.php');

    $error = [];
    $error['username'] = '';
    $error['password'] = '';
    $error['submit'] = '';
    $error['login'] = '';

    $validated_username = '';
    $validated_password = '';

    if (isset($_POST['submit'])) {
        $submit_ok = True;

        if (strlen($_POST['username']) > 3) {
            $validated_username = validateInput($_POST['username']);
        } else {
            $error['username'] = translate('Username error!');
            $submit_ok = False;
        }

        if (strlen($_POST['password']) > 3) {
            $validated_password = validateInput($_POST['password']);
        } else {
            $error['password'] = translate('Password error!');
            $submit_ok = False;
        }

        if ($submit_ok == True) {
            if (strcmp($validated_username, USERNAME) == 0 && strcmp($validated_password, PASSWORD) == 0) {
                $_SESSION['username'] = $validated_username;
                header('Location: products.php');
                exit;
            } else {
                $error['login'] = 'Invalid user or password.';
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
            <input type="text" name="username" value="<?= $validated_username; ?>" placeholder="<?= translate('Username'); ?>"><br>
            <?= $error['username']; ?>

            <input type="password" name="password" value="<?= $validated_password; ?>" placeholder="<?= translate('Password'); ?>"><br>
            <?= $error['password']; ?>

            <input type="submit" name="submit" placeholder="<?= translate('Login'); ?>">
            <?= $error['submit']; ?>
            <?= $error['login']; ?>
        </form>
    </body>
</html>
