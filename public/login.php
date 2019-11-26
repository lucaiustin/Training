<?php
    require_once('../common.php');
    if (isset($_POST['submit'])) {
    }
?>
<html>
    <head>
        <title><?= translate('Login'); ?></title>
    </head>
    <body>
        <form action="login.php" method="post">
            <input type="text" name="username" value="<?= translate('Username'); ?>"><br>
            <input type="password" name="password" value="value="<?= translate('Password'); ?>""><br>
            <input type="submit" name="submit" value="<?= translate('Login'); ?>">
        </form>
    </body>
</html>
