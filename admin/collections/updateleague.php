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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/collections') <= 0){
        http_response_code(404);
        die;
    }
}


if (!isset($_POST['name']) || !isset($_POST['id']) || !isset($_POST['img'])) {
    http_response_code(404);
    die;
}


require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];


$name = mysqli_real_escape_string($conn, trim($_POST['name']));
$id = mysqli_real_escape_string($conn, trim($_POST['id']));
$img = $_POST['img'];

$GLOBALS['script'] = '';


if (empty($name)) {
    $GLOBALS['script'] .= '
        document.querySelector("#update_name").classList.add("invalid")
        document.querySelector("#update_name").nextElementSibling.textContent = "Please fill in this input."
    ';
}

if (empty($img)) {
    $GLOBALS['script'] .= '
        document.querySelector("#input_file_2").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file_2").parentElement.nextElementSibling.textContent = "Please upload an image"
    ';
}

if (!empty($GLOBALS['script'])){
    echo trim($GLOBALS['script']);
    die;
}

if (!is_numeric($id)) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("There is something wrong with the data the was sendt. Please try again!"))
        document.querySelector(".update_errorAll").appendChild(elt)
    ');
    die;
}
if (strlen($id) > 11 || $id < 0) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Couldn\'t update collection, because the collection wasn\'t found!"))
        document.querySelector(".update_errorAll").appendChild(elt)
    ');
    die;
}

if (empty(trim($img))) {
    //Empty image
    echo trim('
        document.querySelector("#input_file_2").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file_2").parentElement.nextElementSibling.textContent = "Please upload an image"
    ');
    die;
}

if (strpos($img, 'image/') === false) {
    //Uploaded file is invalid
    echo trim('
        document.querySelector("#input_file_2").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file_2").parentElement.nextElementSibling.textContent = "Please upload an image"
    ');
    die;
}

if (strpos($img, 'png') === false && strpos($img, 'jpg') === false && strpos($img, 'jpeg') === false){
    //Uploaded file is invalid
    echo trim('
        document.querySelector("#input_file_2").nextElementSibling.classList.add("invalid")
        document.querySelector("#input_file_2").parentElement.nextElementSibling.textContent = "The uploaded file is not supported. Please upload an image with a different filetype."
    ');
    die;
}

//Check if the collection name is valid

if (!preg_match('/^[a-zA-Z0-9 ]*$/i', $name)) {
    $GLOBALS['script'] .= '
        document.querySelector("#update_name").classList.add("invalid")
        document.querySelector("#update_name").nextElementSibling.textContent = "Invalid collection name."
    ';
    die;
}

//Check if the collection exist

$sql = "SELECT null FROM `collections` WHERE `id` = ?;";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong."))
    document.querySelector(".update_errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, 'i', $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("The collection you are trying to update doesn\'t exist"))
        document.querySelector(".update_errorAll").appendChild(elt)
    ';
    die;
}




//Check if the new name exist

$sql = "SELECT null FROM `collections` WHERE `name` = ? AND id != ?;";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong."))
    document.querySelector(".update_errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, "si", $name, $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check > 0) {
    $GLOBALS['script'] .= '
        document.querySelector("#update_name").classList.add("invalid")
        document.querySelector("#update_name").nextElementSibling.textContent = "A collection with the same name exist! Please try a different name."
    ';
    die;
}


//Save image
$imgData = str_replace(' ', '+', $img);
$imgData =  substr($imgData,strpos($imgData,",")+1);
$imgData = base64_decode($imgData);
// Path where the image is going to be saved
$filePath = '../../assets/collections/' . $name . '.png';
// Write $imgData into the image file
$file = fopen($filePath, 'w');
fwrite($file, $imgData) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Failed to write file. Please try again."))
    document.querySelector(".update_errorAll").appendChild(elt)
');
fclose($file);

//Save collection inside the database
$file_name = $name. ".png";

$sql = "UPDATE `collections` SET `name` = ?, `img` = ? WHERE `id` = ?;";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong."))
    document.querySelector(".update_errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, "ssi", $name, $file_name, $id);

mysqli_stmt_execute($stmt);

echo '
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Collection updated successfully."))
    document.querySelector(".update_successAll").appendChild(elt)
';
mysqli_close($conn);