<?php

session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(404);
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clubs</title>
    <link rel="stylesheet" href="update.css">
    <script src="/phone/js/jquery.js"></script>
    <script src="./update.js" defer></script>
</head>
<body>
    <h1 class="title">Update / Delete Clubs</h1>
    <form action="update.php" method="POST" autocomplete="off">
        <div class="input" id="searchBarInput">
            <label for="searchBar">Search Products</label>
            <input type="text" id="searchBar">
            <button type="submit" class="search"></button>
        </div>
    </form>
    <div class="result"></div>
    <div class="club"></div>
    <div class="pwdPopup-parent">
        <form class="pwdPopup" action="./password.php" method="POST">
            <label for="pwd">Type password to confirm</label>
            <input type="password" id="pwd">
            <p class="error"></p>
            <button>Confirm</button>
        </form>
    </div>
    <div class="loading"></div>
</body>
</html>