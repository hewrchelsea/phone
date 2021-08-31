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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/clubs/add') <= 0){
        http_response_code(404);
        die;
    }
}


if (!isset($_POST['data'])) {
    http_response_code(404);
    die;
}

require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];


$data = $_POST['data'];

$data_json = json_decode($_POST['data'], 1);

if (empty($data_json)) {
    //No data sent by the request maker
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, we recieved no data"))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

if (!is_array($data_json)) {
    //The data sent is not an array
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, somethings wrong with the data we recieved"))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}


if (count($data_json) < 1) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, you have to at least add one club"))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

if (count($data_json) > 20) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("You can not add more than 20 clubs at once! Please remove at least '. $count($data_json) - 20 .'"))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

//Loop throught the array
$GLOBALS['script'] = '';

$index = -1;
foreach ($data_json as $d) {
    $index++;
    if (!isset($d['img']) || !isset($d['name']) || !isset($d['league'])) {
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this club. Please delete it.";
        ';
        continue;
    }
    
    if (!preg_match('/^[a-zA-Z0-9 ]*$/i', $d['name'])  || strlen($d['name']) === 0) {
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Invalid name. Please write a valid name.";
        ';
    }

    if (!preg_match('/^[a-zA-Z0-9 ]*$/i', $d['league']) || strlen($d['league']) === 0) {
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "League doesnt exist.";
        ';
    }
    if (empty(trim($d['img'])) || strpos($d['img'], 'image/') === false) {
        //Empty image
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Please upload an image to this club"
        ';
        continue;
    }
    
    if (strpos($d['img'], 'png') === false && strpos($d['img'], 'jpg') === false && strpos($d['img'], 'jpeg') === false){
        //Uploaded file is invalid
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "The uploaded file is not a supported file type."
        ';
        continue;
    }
    //Check if league exist
    $sql = "SELECT `id` FROM `leagues` WHERE `name` = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $d['league']);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $result_check = mysqli_num_rows($result);

        if ($result_check === 0) {
            //League doesn't exist
            $GLOBALS['script'] .= '
                document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412"
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "League doesnt exist.";
            ';
            continue;
        }
        if ($row = mysqli_fetch_assoc($result)) {
            $GLOBALS['league_id'] = $row['id'];
        }

    }else {
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this club. Please delete it.";
        ';
        continue;
    }

    //Check if club exist
    $sql = "SELECT NULL FROM `clubs` WHERE `name` = ? AND `league_id` = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, 'si', $d['name'], $GLOBALS['league_id']);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $result_check = mysqli_num_rows($result);

        if ($result_check > 0) {
            //Club exist
            $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "There is already a club with this name.";
        ';
        continue;
        }

    }else {
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this club. Please delete it.";
        ';
        continue;
    }

}

if (!empty($GLOBALS['script'])) {
    echo $GLOBALS['script'];
    die;
}

$index = -1;

foreach($data_json as $d) {
    $index++;
    
    $sql = "SELECT `id` FROM `leagues` WHERE `name` = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {

        mysqli_stmt_bind_param($stmt, 's', $d['league']);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $result_check = mysqli_num_rows($result);

        if ($result_check === 0) {
            //League doesn't exist
            $GLOBALS['script'] .= '
                document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Couldn\'t add club!";
            ';
            continue;
        }

        if ($row = mysqli_fetch_assoc($result)) {
            add_club($d, $row['id'], $conn, $index);
        }else {
            //League doesn't exist
            $GLOBALS['script'] .= '
                document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Couldn\'t add club!";
            ';
            continue;
        }


    }else {
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Couldn\'t Add this club.";
        ';
        continue;
    }

}


function add_club($d, $id, $conn, $index) {
    $sql = "INSERT INTO `clubs` (`name`, `league_id`, `img`) VALUES(?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        $name = $d['name'];
        $img_name = $name . '.png';

        mysqli_stmt_bind_param($stmt, "sis", $name, $id, $img_name);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_affected_rows($stmt) > 0) {

            //Save the image
            $imgData = str_replace(' ', '+', $d['img']);
            $imgData =  substr($imgData,strpos($imgData,",")+1);
            $imgData = base64_decode($imgData);
            // Path where the image is going to be saved
            $filePath = '../../assets/clubs/' . $img_name;
            // Write $imgData into the image file
            $file = fopen($filePath, 'w');
            fwrite($file, $imgData) or die('
                var elt = document.createElement("li")
                elt.appendChild(document.createTextNode("Failed to write file!"))
                document.querySelector(".errorAll").appendChild(elt)
            ');
            fclose($file);
            $GLOBALS['script'] .= '
                document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #41b169"
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#41b169";
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Added successfully!";
            ';
            return;

        }else {
            $GLOBALS['script'] .= '
                document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
                document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Couldn\'t Add this club.";

            ';
            return;
        }

    }else {
        $GLOBALS['script'] .= '
            document.querySelectorAll(".card")['.$index.'].style.boxShadow = "0px 0px 0px 2px #ee3412"
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").style.color = "#ee3412";
            document.querySelectorAll(".card")['.$index.'].querySelector(".msg").textContent = "Couldn\'t Add this club.";
        ';
        return;
    }
}

echo trim($GLOBALS['script']);
die;