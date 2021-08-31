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
    <script src="/phone/js/jquery.js"></script>
    <script src="add.js" defer></script>
    <title>FOOTYPHONES | ADMIN | CLUBS</title>
</head>
<body>
    <h1 class="title">Add Clubs</h1>
    <form action="./add.php" method="POST" autocomplete="off"> 
        <div class="input">
            <label for="club_name">Club name</label>
            <input type="text" id="club_name">
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="league">League</label>
            <div class="select_parent">
                <select id="league">
                    <option value="0" selected disabled>Not Chosen</option>


                    <?php
                    
                    require_once "../../conn/conn.php";
                    $conn = $GLOBALS['conn'];

                    $sql = "SELECT * FROM `leagues` WHERE 1";
                    $result = mysqli_query($conn, $sql);
                    $result_check = mysqli_num_rows($result); 
                    
                    if ($result_check !== 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <option><?php echo $row['name']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                <div class="custom_select">Not Chosen</div>
            </div>
            <p class="error"></p>
        </div>
        <div class="upload">
            <label for="input_file">Club Logo</label>
            <div class="custom_input_file_parent">
                <input type="file" id="input_file">
                <div class="custom_input_file">Choose File</div>
            </div>
            <p class="error"></p>
        </div>
        <div class="preview" style="display:none">
            <button type="button" class="deleteBtn cross">×</button>
            <img src="//" alt="Print file">
            <h3 class="image_title"></h3>
        </div>

        <button class="addClub" type="button">Add Club</button>
        <div class="added_clubs">
            <h3 class="clubs_title">Added Clubs:</h3>
        </div>
        <ul class="errorAll"></ul>
        <ul class="successAll"></ul>
        <button class="addAll">Add All Clubs</button>
    </form>
    <div class="loading"></div>
</body>
</html>