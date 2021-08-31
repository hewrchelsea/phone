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


if (!isset($_POST['id'])) {
    http_response_code(404);
    die;
}


require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];

$id = mysqli_real_escape_string($conn, trim($_POST['id']));

if (!is_numeric($id)) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("There is some error with the data that was sent! Please reload the page."))
        document.querySelector(".update_errorAll").appendChild(elt)
    ');
    die;
}
if (strlen($id) > 11 || $id < 0) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Couldn\'t delete collection, because the collection wasn\'t found!"))
        document.querySelector(".update_errorAll").appendChild(elt)
    ');
    die;
}

//Check if the collection exist

$sql = "SELECT * FROM `collections` WHERE `id` = ?;";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong"))
    document.querySelector(".update_errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("The collection you are trying to delete is not found!"))
        document.querySelector(".update_errorAll").appendChild(elt)
    ';
    die;
}

$row = mysqli_fetch_assoc($result) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong"))
    document.querySelector(".update_errorAll").appendChild(elt)
');

//Delete all clubs
$sql = "SELECT `id`, `file` FROM `products` WHERE `collection_id` = ?";

$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result_ = mysqli_stmt_get_result($stmt);
$result_check_ = mysqli_num_rows($result_);
while ($r = mysqli_fetch_assoc($result_)) {
    $file_name = '../../product_details/'. $r['file'];

    $url = "../delete/delete.php";

    if (file_exists($file_name)) {    
        
    // set post fields
    $url = 'http://127.0.0.1:81/phone/admin/delete/delete.php';
    // The submitted form data, encoded as query-string-style
    // name-value pairs
    $body = 'id='. $r['id'];
    $c = curl_init ($url);
    curl_setopt ($c, CURLOPT_POST, true);
    curl_setopt ($c, CURLOPT_POSTFIELDS, $body);
    curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
    $page = curl_exec ($c);
    curl_close ($c);
    }    
}

//Delete the collection

$sql = "SELECT `img` FROM `collections` WHERE `id` = ?";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong"))
    document.querySelector(".update_errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Collection deleted successfully"))
        document.querySelector(".update_successAll").appendChild(elt)
    ';
}

if ($row = mysqli_fetch_assoc($result)) {
    if(strlen($row['img']) > 0 && count(explode(".", $row['img'])) > 1) {
        if (file_exists("../../assets/collections/" . $row['img'])) {
            unlink("../../assets/collections/" . $row['img']);
        }
    }
}

$sql = "DELETE FROM `collections` WHERE `id` = ?";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong"))
    document.querySelector(".update_errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Collection deleted successfully"))
        document.querySelector(".update_successAll").appendChild(elt)
    ';
    mysqli_close($conn);
    die;
}
mysqli_close($conn);