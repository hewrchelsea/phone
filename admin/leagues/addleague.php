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


if (!isset($_POST['name']) || !isset($_POST['img'])) {
    http_response_code(404);
    die;
}

require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];


$name = mysqli_real_escape_string($conn, trim($_POST['name']));
$img = $_POST['img'];

$GLOBALS['script'] = '';

if (empty($name)) {
    $GLOBALS['script'] .= '
        document.querySelector("#name").classList.add("invalid")
        document.querySelector("#name").nextElementSibling.textContent = "Please fill in this input."
    ';
}

if (empty($img) || empty(trim($img)) || strpos($img, 'image/') === false) {
    $GLOBALS['script'] .= '
        document.querySelector("#input_file").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file").parentElement.nextElementSibling.textContent = "Please upload an image."
    ';
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}

if (strpos($img, 'png') === false && strpos($img, 'jpg') === false && strpos($img, 'jpeg') === false){
    //Uploaded file is invalid
    $GLOBALS['script'] .= '
        document.querySelector("#input_file").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file").parentElement.nextElementSibling.textContent = "The uploaded file is not a image with supported filetype."
    ';
}

//Check if the league name is valid

if (!preg_match('/^[a-zA-Z0-9 ]*$/i', $name)) {
    $GLOBALS['script'] .= '
        document.querySelector("#name").classList.add("invalid")
        document.querySelector("#name").nextElementSibling.textContent = "Invalid league name."
    ';
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}

//Check if the league exist

$sql = "SELECT null FROM `leagues` WHERE `name` = ?;";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('document.querySelector("#addLeague .errorAll").textContent = "Something went wrong! Please try again later."');

mysqli_stmt_bind_param($stmt, "s", $name);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check > 0) {
    echo trim('
        document.querySelector("#name").classList.add("invalid")
        document.querySelector("#name").nextElementSibling.textContent = "A league with the same name exist! Please try a different name."
    ');
    die;
}


//Save image
$imgData = str_replace(' ', '+', $img);
$imgData =  substr($imgData,strpos($imgData,",")+1);
$imgData = base64_decode($imgData);
// Path where the image is going to be saved
$filePath = '../../assets/leagues/' . $name . '.png';
// Write $imgData into the image file
$file = fopen($filePath, 'w');
fwrite($file, $imgData) or die('document.querySelector("#addLeague .errorAll").textContent = "Failed to write file!"');
fclose($file);

//Save league inside the database
$file_name = $name. ".png";

$sql = "INSERT INTO `leagues` (`name`, `img`) VALUES(?, ?)";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('document.querySelector("#addLeague .errorAll").textContent = "Something went wrong! Please try again later."');

mysqli_stmt_bind_param($stmt, "ss", $name, $file_name);

mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo trim('document.querySelector("#addLeague .successAll").textContent = "League added successfully"');
    die;
}else {
    echo trim('document.querySelector("#addLeague .errorAll").textContent = "Failed to add League. Please try again later."');
    die;
}