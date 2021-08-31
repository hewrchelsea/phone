<?php
session_start();

if (!isset($_SESSION['admin'])) {
    http_response_code(404);
    die;
}else {

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>FOOTYPHONES | ADMIN | DELETE</title>
    <script src="/phone/js/jQuery.js"></script>
    <script src="main.js" defer></script>
</head>
<body>
    <h1 class="title">Search Products</h1>
    <form action="./" method="POST" autocomplete="off">
    
        <div class="input">
            <label for="searchBy">Search By:</label>
            <div class="select_parent">
                <select id="searchBy">
                    <option selected value="name">By Name</option>
                    <option value="id">By Id</option>
                </select>
                <div class="custom_select">By Name</div>
            </div>    
        </div>

        <div class="input" id="searchBarInput">
            <label for="searchBar">Search Products</label>
            <input type="text" id="searchBar">
            <button class="search"></button>
        </div>
        <div class="results"></div>
    </form>
    <div id="product"></div>
    <div class="loading"></div>
</body>
</html>
<?php
}
?>