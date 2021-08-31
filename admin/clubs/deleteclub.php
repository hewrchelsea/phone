<?php

if (isset($_POST['uid']) && isset($_POST['pwd'])) {
    
    include_once "../../conn/conn.php";
    
    $uid = mysqli_real_escape_string($conn, trim($_POST['uid']));
    $pwd = $_POST['pwd'];

    $sql = "SELECT `pwd` FROM `admin` WHERE `uid` = ?";

    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        
        mysqli_stmt_bind_param($stmt, 's', $uid);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $result_check = mysqli_num_rows($result);

        if ($result_check === 0) {
            //Log the user out
            echo "1_";
            die;
        }

        if ($row = mysqli_fetch_assoc($result)) {
            
            $pwd_check = password_verify($pwd, $row['pwd']);

            if ($pwd_check === false) {
                //Wrong password
                //Log the user out
                echo "1_";
                die;
            }
        }
    }else {
        //Log the user out
        echo "1_";
        die;
    }

}else {
    session_start();
    if (!isset($_SESSION['admin']) || !isset($_POST['pwd'])) {
        http_response_code(404);
        die;
    }
    require_once "../../conn/conn.php";
    $conn = $GLOBALS['conn'];
    
    $uid = mysqli_real_escape_string($conn, $_SESSION['admin']);
    $pwd = $_POST['pwd'];

    $sql = "SELECT `pwd` FROM `admin` WHERE `uid` = ?";
    
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {

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

    }else {
        session_unset();
        session_destroy();
        echo '
            alert("Error. You will be logged out")
            window.location.reload()
        ';
        die;
    }
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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin') <= 0){
        http_response_code(404);
        die;
    }
}

if (!isset($_POST['id'])) {
    http_response_code(404);
    die;
}

echo '
    document.querySelector(".pwdPopup-parent").style.display = "none"
    document.querySelector("#pwd").nextElementSibling.textContent = ""
';

require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];

$id = mysqli_real_escape_string($conn, trim($_POST['id']));

if (!is_numeric($id)) {
    echo "document.querySelector('.errorAll').textContent = 'There is some error with the data that was sent! Please reload the page.'";
    die;
}
if (strlen($id) > 11 || $id < 0) {
    echo "document.querySelector('.errorAll').textContent = 'Couldn't delete club, because the club wasn't found!'";
    die;
}

//Check if the club exist

$sql = "SELECT * FROM `clubs` WHERE `id` = ?;";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die("document.querySelector('.errorAll').textContent = 'Error connecting to the server. Please try again later.'");

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    echo "document.querySelector('.errorAll').textContent = 'Club not found!'";
    die;
}
$row = mysqli_fetch_assoc($result) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Error, something went wrong"))
    document.querySelector(".update_errorAll").appendChild(elt)
');

//Delete all products
$sql = "SELECT `id`, `file` FROM `products` WHERE `club_id` = ?";

$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result_ = mysqli_stmt_get_result($stmt);
$result_check_ = mysqli_num_rows($result_);
while ($r = mysqli_fetch_assoc($result_)) {
    $file_name = '../../product_details/'. $r['file'];
    if (file_exists($file_name)) {    
        // set post fields
        $url = 'http://127.0.0.1:81/phone/admin/products/update/delete.php';

        $c = curl_init ($url);
        curl_setopt ($c, CURLOPT_POST, true);

        if (isset($_POST['uid']) && isset($_POST['pwd'])) {
            $body = 'id=' . $r['id'] . '&uid=' . $uid . '&pwd=' . $pwd;
            curl_setopt ($c, CURLOPT_POSTFIELDS, $body);
        }else {
            $body ='id=' . $r['id'] . '&uid=' . $uid . '&pwd=' . $pwd;
            curl_setopt ($c, CURLOPT_POSTFIELDS, $body);
        }

        curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
        $page = curl_exec ($c);
        curl_close ($c);

        if ($page == '1_') {
            if (isset($_SESSION['admin'])) {
                //Log out
                session_unset();
                session_destroy();
                echo '
                    alert("Error. You will be logged out")
                    window.location.reload()
                ';
                die;
            }else {
                //Tell other pages to log out
                echo '1_';
                die;
            }
        }
        echo $page;
    }    
}
if (file_exists('../../assets/clubs/' . $row['img'])) {
    unlink('../../assets/clubs/' . $row['img']);
}


$sql = "DELETE FROM `clubs` WHERE `id` = ?";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die("document.querySelector('.errorAll').textContent = 'Error connecting to the server. Please try again later.'");

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

echo "document.querySelector('.successAll').textContent = 'Club deleted successfully.'";