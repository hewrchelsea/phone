<?php
session_start();

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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin') <= 0){
        http_response_code(404);
        die;
    }
}

if (!isset($_POST['uid']) || !isset($_POST['pwd']) || !isset($_POST['sk'])) {
    http_response_code(404);
    die;
}

require_once "../conn/conn.php";
$conn = $GLOBALS['conn'];

$uid = mysqli_real_escape_string($conn, trim($_POST['uid']));
$pwd = $_POST['pwd'];
$sk = $_POST['sk'];

if (empty($uid) || empty($pwd) || empty($sk)) {
    echo "empty";
    die;
}

if (strlen($uid) <= 4) {
    echo "One or more of the values given is wrong! Please try again.";
    die;
}

$number = preg_match('@[0-9]@', $pwd);
$uppercase = preg_match('@[A-Z]@', $pwd);
$lowercase = preg_match('@[a-z]@', $pwd);
$specialChars = preg_match('@[^\w]@', $pwd);
 
if(strlen($pwd) < 8 || !$number || !$uppercase || !$lowercase || !$specialChars) {
    echo "One or more of the values given is wrong! Please try again.";
    die;
}

if (strlen($sk) <= 5) {
    echo "One or more of the values given is wrong! Please try again.";
    die;
}

$sql = "SELECT * FROM `admin` WHERE `uid` = ?";
$stmt = mysqli_stmt_init($conn);

if(mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, 's', $uid);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $result_check = mysqli_num_rows($result);

    if ($result_check == 0) {

        echo "One or more of the values given is wrong! Please try again.";
        die;
    }
    if ($row = mysqli_fetch_assoc($result)) {
        $password_check = password_verify($pwd, $row['pwd']);

        if ($password_check === false) {
            echo "One or more of the values given is wrong! Please try again.";
            die;
        }
        $sk_check = password_verify($sk, $row['sk']);
        if ($sk_check === false) {
            echo "One or more of the values given is wrong! Please try again.";
            die;
        }
        //Login success
        $_SESSION['admin'] = $row['uid'];
        echo "success";
    }
}