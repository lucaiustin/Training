<?php
    require_once('../common.php');
    if (isset($_POST['submit'])) {
        $validated_username = validateInput($_POST['username']);
        $validated_password = validateInput($_POST['password']);
        if (strcmp($validated_username, USERNAME) == 0 && strcmp($validated_password, PASSWORD) == 0) {
            session_start();
            $_SESSION['username'] = $validated_username;
            header('Location: products.php');
            exit;
        }
    }
?>
<html>
    <head>
        <title><?= translate('Login'); ?></title>
    </head>
    <body>
        <form action="login.php" method="post">
            <input type="text" name="username" value="<?= translate('Username'); ?>"><br>
            <input type="password" name="password" value="value="<?= translate('Password'); ?>"><br>
            <input type="submit" name="submit" value="<?= translate('Login'); ?>">
        </form>
    </body>
</html>
