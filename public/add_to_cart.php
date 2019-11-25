<?php
    $id = $_GET["id"];
    if(isset($id))
    {
        session_start();
        $session_array = $_SESSION['cart'];
        array_push($session_array, $id);
        $_SESSION['cart'] = $session_array;
    }
    header('Location: index.php');
?>
