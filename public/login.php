<?php
    require_once('../common.php');
    if (isset($_POST['submit'])) {
        $validated_username = validateInput($_POST['username']);
        $validated_password = validateInput($_POST['password']);
        if (strcmp($validated_username, USERNAME) == 0 && strcmp($validated_password, PASSWORD) == 0) {
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
            <input type="text" name="username" placeholder="<?= translate('Username'); ?>"><br>
            <input type="password" name="password" placeholder="<?= translate('Password'); ?>"><br>
            <input type="submit" name="submit" placeholder="<?= translate('Login'); ?>">
        </form>
    </body>
</html>
