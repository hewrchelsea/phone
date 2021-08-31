<?php
session_start();

if (!isset($_SESSION['admin'])) {
    //Not logged in
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <script src="/phone/js/jquery.js"></script>
    <script src="/phone/js/header.js" defer></script>
    <script src="./js/main.js" defer></script>
    <title>FOOTYPHONES | ADMIN</title>
</head>
<body>
    <header>
        <img src="/phone/images/icons/logo.png" alt="FOOTYPHONES" class="logo">
        <div class="right">
            <a href="javascript:void(0)" class="search" title=""><img src="/phone/images/icons/search.png"></a>
            <a href="/phone/shop/cart" class="cart" title=""><img src="/phone/images/icons/cart.png"></a>
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
    <div class="background"></div>
</body>
</html>

<?php
}else {
    //Logged in

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/admin.css">
    <script src="/phone/js/jquery.js"></script>
    <script src="./js/admin.js" defer></script>
    <title>FOOTYPHONES | ADMIN</title>
</head>
<body>
    <h1 class="title">Admin Dashboard</h1>
    
    <section>
        <a href="./products" class="link">
            <h2 class="link_title">Manage Products</h2>
            <div class="icon box"></div>
        </a>
        <a href="./leagues" class="link">
            <h2 class="link_title">Manage Leagues</h2>
            <div class="icon league"></div>
        </a>
        <a href="./clubs" class="link">
            <h2 class="link_title">Manage Clubs</h2>
            <div class="icon club"></div>
        </a>
        <a href="./collections" class="link">
            <h2 class="link_title">Manage Collections</h2>
            <div class="icon collection"></div>
        </a>
    </section>
</body>
</html>
<?php
}
?>