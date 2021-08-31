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
    <link rel="stylesheet" href="style.css">
    <title>FOOTYPHONES | ADMIN | DELETE</title>
    <script src="/phone/js/jQuery.js"></script>
    <script src="main.js" defer></script>
</head>
<body>
    <h1 class="title">Manage Collections</h1>
    <div class="open addLeague" data-open="addLeague_section">
            Add Collection
            <div class="icon">
                <div></div>
                <div></div>
            </div>
    </div>
    <form action="./addLeague.php" method="POST" class="addLeague_section" id="addLeague" style="display: none" autocomplete="off">
        <div class="input">
            <label for="name">Collection Name</label>
            <input type="text" id="name">
            <p class="error"></p>
        </div>
        <div class="upload">
            <label for="input_file">Collection Logo</label>
            <div class="custom_input_file_parent">
                <input type="file" id="input_file">
                <div class="custom_input_file">Choose File</div>
            </div>
            <p class="error"></p>
        </div>
        <div class="preview">
            <button type="button" class="deleteBtn cross">×</button>
            <img src="//" alt="Print file">
            <h3 class="image_title"></h3>
        </div>
        <div class="errorAll"></div>
        <div class="successAll"></div>
        <button type="submit">Add Collection</button>
    </form>
    <div class="open updateDelete" data-open="updateDelete_section">
        Update / Delete
        <div class="icon">
            <div></div>
            <div></div>
        </div>
    </div>
    <form action="./searchLeagues.php" method="POST" id="search_league" class="updateDelete_section" autocomplete="off">
        <div class="input" id="searchBarInput">
            <label for="searchBar">Search Collections</label>
            <input type="text" id="searchBar">
            <button type="submit" class="search"></button>
        </div>
        <div class="result"></div>
        <div class="update">
            
        </div>
    </form>
    <div class="loading"></div>
</body>
</html>