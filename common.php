<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');

    require_once('config.php');

    $dbh = new PDO($dsn, $user, $pass);

    function translate($str) {
        return $str;
    }
