<?php
try {
    $user = 'root';
    $pass = '';
    $dbh = new PDO('mysql:host=localhost;dbname=test', $user, $pass);
    $stmt  = $dbh->query('SELECT * from products');

    session_start();
    #$_SESSION['cart'] = [];
    if(!isset($_SESSION["cart"])) {
        $_SESSION['cart'] = [];
    }

    $session_cart_ids = $_SESSION['cart'];
    $products = [];
    while($row = $stmt->fetch())
    {
        $gasit = 1;
        foreach ($session_cart_ids as $session_product_id)
        {
            if($session_product_id == $row['id']) {
                $gasit = 0;
            }
        }
        if($gasit == 1)
        {
            array_push($products, $row);
        }
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>

<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
    <body>
        <div class = "container">
            <div class = "product-list">
                <?php foreach($products as $product): ?>
                    <div class = "product">
                        <div class = "product-image">
                             <img src = 'images/<?php echo $product['image_name']; ?>'>
                        </div>
                        <div class = "product-info">
                            <?php
                                echo $product['id'];
                                echo $product['title'];
                            ?>
                        </div>
                        <a href = '/add_to_cart.php?id=<?php echo $product['id']; ?>'>Add</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href = ''>Go to cart</a>
        </div>
    </body>
</html>
