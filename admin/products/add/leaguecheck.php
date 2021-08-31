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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/products/') <= 0){
        http_response_code(404);
        die;
    }
}

if (!isset($_POST['league'])) {
    http_response_code(404);
    die;
}

require_once "../../../conn/conn.php";
$conn = $GLOBALS['conn'];

$league = mysqli_real_escape_string($conn, $_POST['league']);

if (empty($league)) {
    echo '
        document.querySelector("#league").nextElementSibling.classList.add("invalid")
        document.querySelector("#league").parentElement.nextElementSibling.textContent = "The league seems to be empty! Please choose a league."
    ';
    die;
}

//Check if league exist

$sql = "SELECT `id` FROM `leagues` WHERE `name` = ?";

$stmt = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmt, $sql)) {

    mysqli_stmt_bind_param($stmt, 's', $league);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $result_check = mysqli_num_rows($result);

    if ($result_check == 0) {
        echo '
            document.querySelector("#league").nextElementSibling.classList.add("invalid")
            document.querySelector("#league").parentElement.nextElementSibling.textContent = "The selected league is not found! Please choose a different league or refresh the page."
        ';
        die;
    }

    if ($row = mysqli_fetch_assoc($result)) {
        $sql = "SELECT `name` FROM `clubs` WHERE `league_id` = '" .$row['id']."';";
        $result = mysqli_query($conn, $sql);
        
        $result_check = mysqli_num_rows($result);

        if ($result_check == 0) {
            echo '
                document.querySelector("#league").nextElementSibling.classList.add("invalid")
                document.querySelector("#league").parentElement.nextElementSibling.textContent = "The selected league doesn\'t have any clubs! Please choose a different league"
            ';
            die;
        }

        $GLOBALS['script'] = '';
        while ($row = mysqli_fetch_assoc($result)) {
            $GLOBALS['script'] .= '
                var club = document.querySelector("#club")
                var elt = document.createElement("option")
                elt.appendChild(document.createTextNode("'.$row['name'].'"))
                club.appendChild(elt)
                club.parentElement.parentElement.style.display = "flex"
            ';
        }
    }
    echo $GLOBALS['script'];

    mysqli_close($conn);

}else {
    //Error in the query or server    
    echo '
        document.querySelector("#league").nextElementSibling.classList.add("invalid")
        document.querySelector("#league").parentElement.nextElementSibling.textContent = "Error, something went wrong!"
    ';
    die;
}