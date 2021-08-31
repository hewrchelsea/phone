<?php

$app->get('/cart[/]', function ($request, $response, $args) {
?>

<head>
<link rel="stylesheet" href="/phone/style/cart.css">
<script src="/phone/js/jQuery.js" defer></script>
<script src="/phone/shop/js/cart.js" defer></script>
</head>

<div class="cart_content">
    <div class="empty" style="display:none">
        <h1 class="title">You have nothing in the card!</h1>
        <a href="/phone">Continue Shopping</a>
    </div>
</div>


<?php
});
?>