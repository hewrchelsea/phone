<?php

if (isset($_POST['uid']) && isset($_POST['pwd'])) {
    
    include_once "../../../conn/conn.php";
    
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
    if (!isset($_SESSION['admin'])) {
        http_response_code(404);
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

require_once "../../../conn/conn.php";
$conn = $GLOBALS['conn'];


$id = mysqli_real_escape_string($conn, trim($_POST['id']));

if (!is_numeric($id)) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Product is not found. Please try again later."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}

if (strlen($id) > 11 || $id < 0) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Product is not found. Please try again later."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}

//Check if the product exist

$sql = "SELECT `file` FROM `products` WHERE `id` = ?;";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Something went wrong. Please try again later."))
    document.querySelector(".errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Product is not found. Please try a different product."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}

//Open the json file of the product

$row = mysqli_fetch_assoc($result);

if (!file_exists('../../../product_details/' . $row['file'])) {
    
    $sql = "DELETE FROM `products` WHERE id = ?";

    $stmt = mysqli_stmt_init($conn);

    mysqli_stmt_prepare($stmt, $sql) or die('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Something went wrong. Please try again later."))
        document.querySelector(".errorAll").appendChild(elt)
    ');

    mysqli_stmt_bind_param($stmt, "i", $id);

    mysqli_stmt_execute($stmt);
    
    if (mysqli_stmt_affected_rows($stmt) === 1) {   
        echo '
            var elt = document.createElement("li")
            elt.appendChild(document.createTextNode("Something was wrong with the chosen product, but it is deleted successfully"))
            document.querySelector(".successAll").appendChild(elt)
        ';
        die;
    }else {
        echo '
            var elt = document.createElement("li")
            elt.appendChild(document.createTextNode("Something went wrong. Please try again later."))
            document.querySelector(".errorAll").appendChild(elt)
        ';
        die;
    }
}

$file = fopen('../../../product_details/' . $row['file'], 'r') or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Unable to open the file of this product. Please try again later."))
    document.querySelector(".errorAll").appendChild(elt)
');

$file_content = fread($file, filesize('../../../product_details/' . $row['file']));
fclose($file);

$json = json_decode($file_content, 1);

if (isset($json['print_files']) && is_array($json['print_files'])) {

    foreach ($json['print_files'] as $p) {
        if (isset($p['img'])) {
            if (file_exists('../../../assets/print/' . $p['img'])) {
                unlink('../../../assets/print/' . $p['img']);
            }
        }
    }

}

if (isset($json['mockups']) && is_array($json['mockups'])) {

    foreach ($json['mockups'] as $m) {
        if (file_exists('../../../assets/mockups/92/' . $m)) {
            unlink('../../../assets/mockups/92/' . $m);
        }
        if (file_exists('../../../assets/mockups/156/' . $m)) {
            unlink('../../../assets/mockups/156/' . $m);
        }
        if (file_exists('../../../assets/mockups/350/' . $m)) {
            unlink('../../../assets/mockups/350/' . $m);
        }
    }
}

//Delete the product from the database

$sql = "DELETE FROM `products` WHERE `id` = ?;";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Something went wrong. Please try again later."))
    document.querySelector(".errorAll").appendChild(elt)
');

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

unlink('../../../product_details/' . $row['file']) or die("Unable to delete the product please try again later.");

echo '
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Product deleted successfully."))
    document.querySelector(".successAll").appendChild(elt)
';
die;