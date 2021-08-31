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


if (!isset($_POST['name']) || !isset($_POST['id']) || !isset($_POST['img']) || !isset($_POST['league'])) {
    http_response_code(404);
    die;
}


require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];


$name = mysqli_real_escape_string($conn, trim($_POST['name']));
$id = mysqli_real_escape_string($conn, trim($_POST['id']));
$league = mysqli_real_escape_string($conn, trim($_POST['league']));
$img = $_POST['img'];

$GLOBALS['script'] = '';

if (empty($name)) {
    $GLOBALS['script'] .= '
        document.querySelector("#club_name").classList.add("invalid")
        document.querySelector("#club_name").nextElementSibling.textContent = "Please fill in this input.";
    ';
}

if (empty($img)) {
    $GLOBALS['script'] .= '
        document.querySelector("#input_file").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file").parentElement.nextElementSibling.textContent = "Please upload an image.";
    ';
}

if (!is_numeric($id) || (strlen($id) > 11 || $id < 0)) {
    $GLOBALS['script'] .= '
        document.querySelector(".errorAll").textContent = "There is some error with the data that was sent! Please reload the page.";
    ';
}

if (empty($league)) {
    $GLOBALS['script'] .= '
        document.querySelector("#league").nextElementSibling.classList.add("invalid")
        document.querySelector("#league").parentElement.nextElementSibling.textContent = "Please fill in this input.";
    ';
}


if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}

//Check if club exist

$sql = "SELECT null FROM `clubs` WHERE id = ?;";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    document.querySelector(".errorAll").textContent = "Error, something went wrong!"
');

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    echo '
        document.querySelector(".errorAll").textContent = "This club doesn\'t exist"
    ';
    die;

}



if (!preg_match('/^[a-zA-Z0-9 ]*$/i', $league)) {
    
    $GLOBALS['script'] .= '
        document.querySelector("#league").nextElementSibling.classList.add("invalid")
        document.querySelector("#league").parentElement.nextElementSibling.textContent = "The league is not found! Please choose a different league";
    ';
    echo trim($GLOBALS['script']);
    die;
}



if (strlen($id) > 11 || $id < 0) {

    $GLOBALS['script'] .= '
        document.querySelector(".errorAll").textContent = "Couldn\'t update club, because the club wasn\'t found!"
    ';
    echo trim($GLOBALS['script']);

    die;
}


if (empty(trim($img))) {
    //Empty image
    $GLOBALS['script'] .= '
        document.querySelector("#input_file").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file").parentElement.nextElementSibling.textContent = "Please upload an image.";
    ';
    echo trim($GLOBALS['script']);
    die;
}

if (strpos($img, 'image/') === false) {
    //Uploaded file is invalid
    $GLOBALS['script'] .= '
        document.querySelector("#input_file").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file").parentElement.nextElementSibling.textContent = "Please upload an image.";
    ';
    echo trim($GLOBALS['script']);
    die;
}

if (strpos($img, 'png') === false && strpos($img, 'jpg') === false && strpos($img, 'jpeg') === false){
    //Uploaded file is invalid
    $GLOBALS['script'] .= '
        document.querySelector("#input_file").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file").parentElement.nextElementSibling.textContent = "The uploaded file is not supported. Please upload an image with a different filetype.";
    ';
    echo trim($GLOBALS['script']);
    die;
}

//Check if the club name is valid

if (!preg_match('/^[a-zA-Z0-9 ]*$/i', $name)) {
    $GLOBALS['script'] .= '
        document.querySelector("#club_name").classList.add("invalid")
        document.querySelector("#club_name").nextElementSibling.textContent = "The club name is invalid! Please write a valid name.";
    ';
    echo trim($GLOBALS['script']);
    die;
}

//Check if the league exist

$sql = "SELECT `id` FROM `leagues` WHERE `name` = ?;";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('document.querySelector(".errorAll").textContent = "Something went wrong. Please try again later."');

mysqli_stmt_bind_param($stmt, "s", $league);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check === 0) {

    $GLOBALS['script'] .= '
        document.querySelector("#league").nextElementSibling.classList.add("invalid")
        document.querySelector("#league").parentElement.nextElementSibling.textContent = "'.$league.'";
    ';
    echo trim($GLOBALS['script']);
    die;
}

$GLOBALS['league_id'] = '';

if ($row = mysqli_fetch_assoc($result)) {
    $GLOBALS['league_id'] = $row['id'];
}


//Check if the club exist

$sql = "SELECT null FROM `clubs` WHERE `name` = ? AND id != ?;";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('document.querySelector(".errorAll").textContent = "Something went wrong. Please try again later."');

mysqli_stmt_bind_param($stmt, "si", $name, $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check > 0) {
    $GLOBALS['script'] .= '
        document.querySelector("#club_name").classList.add("invalid")
        document.querySelector("#club_name").nextElementSibling.textContent = "A club with the same name exist! Please try a different name.";
    ';
    die;
}


//Save image
$imgData = str_replace(' ', '+', $img);
$imgData =  substr($imgData,strpos($imgData,",")+1);
$imgData = base64_decode($imgData);
// Path where the image is going to be saved
$filePath = '../../assets/clubs/' . $name . '.png';
// Write $imgData into the image file
$file = fopen($filePath, 'w');
fwrite($file, $imgData) or die('document.querySelector(".errorAll").textContent = "Failed to write file! Please try again a different time."');
fclose($file);

//Save club inside the database
$file_name = $name. ".png";

$sql = "UPDATE `clubs` SET `name` = ?, `img` = ?, `league_id` = ? WHERE `id` = ?;";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('document.querySelector(".errorAll").textContent = "Something went wrong. Please try again later."');

mysqli_stmt_bind_param($stmt, "ssii", $name, $file_name, $GLOBALS['league_id'], $id);

mysqli_stmt_execute($stmt);

$GLOBALS['script'] .=  'document.querySelector(".successAll").textContent = "Club updated successfully."';

echo trim($GLOBALS['script']);