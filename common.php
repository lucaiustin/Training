<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 'on');

    require_once('config.php');

    $dbh = new PDO(DNS, DB_USER, DB_PASS);

    session_start();

    function translate($str) {
        return $str;
    }

    function validateInput($data) {
        $data = strip_tags($data);
        return $data;
    }
