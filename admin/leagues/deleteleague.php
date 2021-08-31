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


if (!isset($_POST['id']) || !isset($_POST['pwd'])) {
    http_response_code(404);
    die;
}


require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];

$id = mysqli_real_escape_string($conn, trim($_POST['id']));
$pwd = $_POST['pwd'];
$uid = $_SESSION['admin'];


if (!is_numeric($id)) {
    echo trim('
        document.querySelector(".pwdPopup-parent").style.display = "none"
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("There is some error with the data that was sent! Please reload the page."))
        document.querySelector(".update_errorAll").appendChild(elt)
    ');
    die;
}
if (strlen($id) > 11 || $id < 0) {
    echo trim('
        document.querySelector(".pwdPopup-parent").style.display = "none"
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Couldn\'t delete league, because the league wasn\'t found!"))
        document.querySelector(".update_errorAll").appendChild(elt)
    ');
    die;
}

//Check if the password is correct

$sql = "SELECT * FROM `admin` WHERE `uid` = ?";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong"))
    document.querySelector(".update_errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, 's', $uid);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    session_unset();
    session_destroy();
    echo '
        alert("Error. You will be logged out")
        window.location.reload()
    ';
    die;
}

if ($row = mysqli_fetch_assoc($result)) {
    $pwd_check = password_verify($pwd, $row['pwd']);
    if ($pwd_check === false) {
        echo '
            document.querySelector("#pwd").classList.add("invalid")
            document.querySelector("#pwd").nextElementSibling.textContent = "Wrong Password!"
            document.querySelector(".pwdPopup-parent").style.display = "flex"
        ';
        die;
    }
}

echo '
    document.querySelector(".pwdPopup-parent").style.display = "none"
    document.querySelector("#pwd").nextElementSibling.textContent = ""
';

//Check if the league exist

$sql = "SELECT * FROM `leagues` WHERE `id` = ?;";
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
        elt.appendChild(document.createTextNode("The league you are trying to delete is not found!"))
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
$sql = "SELECT `id` FROM `clubs` WHERE `league_id` = ?";

$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result_ = mysqli_stmt_get_result($stmt);
$result_check_ = mysqli_num_rows($result_);

while ($r = mysqli_fetch_assoc($result_)) {

    // set post fields
    $url = 'http://127.0.0.1:81/phone/admin/clubs/deleteclub.php';

    $body = 'id='. $r['id'] .'&uid=' . $uid . '&pwd=' . $pwd;
    $c = curl_init ($url);
    curl_setopt ($c, CURLOPT_POST, true);
    curl_setopt ($c, CURLOPT_POSTFIELDS, $body);
    curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);

    $page = curl_exec($c);
    curl_close ($c);
    if ($page == '1_') {
        //Log the user out
        session_unset();
        session_destroy();
        echo '
            alert("Something went wrong during the process. You will be logged out!")
            window.location.reload()
        ';
        die;
    }
}
//Delete the league image

$sql = "SELECT `img` FROM `leagues` WHERE `id` = ?";
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
        elt.appendChild(document.createTextNode("League deleted successfully"))
        document.querySelector(".update_successAll").appendChild(elt)
    ';
}

if ($row = mysqli_fetch_assoc($result)) {
    if(strlen($row['img']) > 0 && count(explode(".", $row['img'])) > 1) {
        if (file_exists("../../assets/leagues/" . $row['img'])) {
            unlink("../../assets/leagues/" . $row['img']);
        }
    }
}

$sql = "DELETE FROM `leagues` WHERE `id` = ?";
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
        elt.appendChild(document.createTextNode("League and all clubs and products associated with the league deleted successfully"))
        document.querySelector(".update_successAll").appendChild(elt)
    ';
    mysqli_close($conn);
    die;
}
mysqli_close($conn);