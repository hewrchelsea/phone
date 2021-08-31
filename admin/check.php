<?php

if (isset($_SERVER['HTTP_ORIGIN']) && isset($_SERVER['HTTP_HOST'])) {
    if (strpos($_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_HOST']) <= 0) {
        http_response_code(404);
        die;
    }
}


if (isset($_SERVER['HTTP_SEC_FETCH_SITE'])){
    if ($_SERVER['HTTP_SEC_FETCH_SITE'] != 'same-origin'){
        http_response_code(404);
        die;
    }
}

if (isset($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], '/admin') <= 0){
        http_response_code(404);
        die;
    }
}

if (!isset($_POST['data'])) {
    http_response_code(404);
    die;
}

?>

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
<div class="announcement">
    <div class="closeBtn">×</div>
    <p>
        This page is only for admins, if you are not a admin, kindly leave this page. We will take leagal action to any unknown logins. <a href="/phone">Return to Homepage</a>
    </p>
</div>
<div class="login">
    <h1 class="title">Login</h1>
    <form action="./login.php" method="POST" autocomplete="off">

    <div class="input">
        <label for="uid">Username</label>
        <input type="text" id="uid">
    </div>
    <div class="input">
        <label for="pwd">Password</label>
        <input type="password" id="pwd">
    </div>
    <div class="input">
        <label for="sk">Secret key</label>
        <input type="password" id="sk">
    </div>
    <button id="login">Login</button>
    </form>
</div>

<script type="text/javascript">
    let uid = document.querySelector("#uid")
    let pwd = document.querySelector("#pwd")
    let sk = document.querySelector("#sk")
    const submit = document.querySelector("#login")


    $('form').submit(e => {
        e.preventDefault()
        $.post("./login.php", {
            uid: uid.value,
            pwd: pwd.value,
            sk: sk.value
        }, data => {
            if (data == "success") {
                window.location.reload()
            }else {
                alert(data)
                let inputs = document.querySelectorAll('input')
                inputs.forEach(input => {
                    if (input.value.trim().length === 0) {
                        input.classList.add('invalid')
                    }else {
                        input.classList.remove('invalid')
                    }
                })
            }
        })
    })    
    document.querySelector(".closeBtn").onclick = e => {
        e.target.parentElement.remove()
    }
</script>