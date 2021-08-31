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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/leagues') <= 0){
        http_response_code(404);
        die;
    }
}


if (!isset($_POST['name']) || !isset($_POST['check'])) {
    http_response_code(404);
    die;
}

require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];


$name = mysqli_real_escape_string($conn, trim($_POST['name']));
$check = $_POST['check'];

$accepted_check_values = Array('clicked', 'notClicked');


if (!in_array($check, $accepted_check_values)) {
    http_response_code(404);
    die;
}


if (empty($name)) {
    if ($check == 'clicked') {
        echo "<p class='msg'>Please fill in all the inputs.</p>";
        echo "
            <script type=\"text/javascript\">
                document.querySelector('.result').style.display = 'flex'
                document.querySelector('#searchBar').classList.add('invalid')
            </script>";
    }else {
        echo "<script type=\"text/javascript\">document.querySelector('.result').style.display = 'none'</script>";
    }
    die;
}

echo "
    <script type=\"text/javascript\">
        document.querySelector('.result').style.display = 'flex'
        document.querySelector('#searchBar').classList.remove('invalid')
    </script>";

if (!preg_match('/^[a-zA-Z0-9 ]*$/i', $name)) {
    echo "<p class='msg'>No league found with the given name.</p>";
    die;
}

//Check if league exist

$sql = "SELECT * FROM `leagues` WHERE `name` LIKE ? ESCAPE '*' LIMIT 0, 5;";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die("<p class='msg'>Error, something went wrong! Please try again later.</p>");

$name .= '%';

$newName = '*' . $name;

mysqli_stmt_bind_param($stmt, 's', $newName);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    //No result
    echo "<p class='msg'>No league found with the given name.</p>";
    die;
}
//League found

while ($row = mysqli_fetch_assoc($result)) {
    echo '
    <div class="item" onclick="loadData(this)" data-id="'.$row['id'].'">
        <p class="item-name">'.$row['name'].'</p>
    </div>
    ';
}