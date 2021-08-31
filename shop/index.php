<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/phone/js/jquery.js"></script>
    <script src="/phone/js/header.js" defer></script>
    <title>FOOTYPHONES | SHOP</title>
</head>
<body>
    <header>
        <img src="/phone/images/icons/logo.png" alt="FOOTYPHONES" class="logo">
        <div class="right">
            <a href="javascript:void(0)" class="search" title=""><img src="/phone/images/icons/search.png"></a>
            <a href="/phone/shop/cart" class="cart" title=""><div class="cart_items_num"></div><img src="/phone/images/icons/cart.png"></a>
            <div class="burger">
                <div></div>
                <div></div>
            </div>
            <div class="nav_parent">
                <div class="nav">
                    <a href="#">HOME</a>
                    <a href="#">ACCOUNT</a>
                    <a href="#">ABOUT US</a>
                    <p class="copy">© 2021. ALL RIGHTS RESERVED. FOOTYPHONES.</p>
                </div>
            </div>
        </div>
    </header>

<?php

require './vendor/autoload.php';

$app = $app = new \Slim\App(['settings' => ['displayErrorDetails' => true, 'addContentLengthHeader' => false]]);



require_once "./shop.php";
require_once "./leagues.php";
require_once "../conn/conn.php";
require_once "./clubs.php";
require_once "./products.php";
require_once "./cart.php";

$app->run();

?>


<footer>
        <h2 class="title">Navigation</h2>
        <a href="#">Privacy Policy</a>
        <a href="#">Refund Policy</a>
        <a href="#">Terms & Conditions</a>
        <a href="#">Shipping & Returns</a>
        <a href="#">FAQ</a>
        <a href="#">Contact Us</a>
        <p class="copy">© 2021. ALL RIGHTS RESERVED. FOOTYPHONES.</p>

</footer>

<div class="notification">
    <div class="top close">×</div>
    <p class="text">Item added to cart successfully.</p>
</div>

</body>
</html>