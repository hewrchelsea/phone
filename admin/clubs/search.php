<?php

session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(404);
    die;
}

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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/clubs/update') <= 0){
        http_response_code(404);
        die;
    }
}


if (!isset($_POST['name']) || !isset($_POST['click'])) {
    http_response_code(404);
    die;
}

require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];


$name = mysqli_real_escape_string($conn, trim($_POST['name']));
$click = $_POST['click'];

$allowed_click = [
    'notClicked',
    'clicked'
];

if (!in_array($click, $allowed_click)) {
    echo "Error. Something is misssing in this request!";
    die;
}



if (empty($name)) {
    if ($click == 'clicked') {
        echo "<p class='error'>Please fill in the input.</p>";
    }
    die;
}

if (!preg_match('/^[a-zA-Z0-9 ]*$/i', $name)) {
    echo "No club found!";
}


$sql = "SELECT * FROM `clubs` WHERE `name` LIKE ? ESCAPE '*' LIMIT 0, 5;";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die("<p class='error'>Error, something went wrong! Please try again later.</p>");

$name .= '%';

$newName = '*' . $name;

mysqli_stmt_bind_param($stmt, 's', $newName);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    echo "No club found!";
    die;
}

while ($row = mysqli_fetch_assoc($result)) {
    ?>
    <div class="item" onclick="loadData(this)" data-id="<?php echo $row['id']; ?>">
        <p class="item-name"><?php echo $row['name']; ?></p>
    </div>    
    <?php
}