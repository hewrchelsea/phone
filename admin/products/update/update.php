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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/products/update') <= 0){
        http_response_code(404);
        die;
    }
}


if (!isset($_POST['product_name']) || !isset($_POST['product_price']) || !isset($_POST['discount']) || !isset($_POST['phones']) || !isset($_POST['variants']) || !isset($_POST['league']) || !isset($_POST['club']) || !isset($_POST['collection']) || !isset($_POST['mockups']) || !isset($_POST['mockups_92']) || !isset($_POST['mockup_156']) || !isset($_POST['id'])) {
    echo "Hello";
    http_response_code(404);
    die;
}

require_once "../../../conn/conn.php";
$conn = $GLOBALS['conn'];


$product_name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
$product_price = mysqli_real_escape_string($conn, trim($_POST['product_price']));
$discount = mysqli_real_escape_string($conn, trim($_POST['discount']));
$league = mysqli_real_escape_string($conn, trim($_POST['league']));
$club = mysqli_real_escape_string($conn, trim($_POST['club']));
$collection = mysqli_real_escape_string($conn, trim($_POST['collection']));
$id = mysqli_real_escape_string($conn, trim($_POST['id']));


$phones = $_POST['phones'];
$variants = $_POST['variants'];
$mockups = $_POST['mockups'];

$mockups_92 = $_POST['mockups_92'];

$mockup_156 = $_POST['mockup_156'];


$json_phone = json_decode($phones, true);

$json_variants = json_decode($variants, true);

$json_mockups = json_decode($mockups, true);

$json_mockups_92 = json_decode($mockups_92, true);

$json_mockup_156 = json_decode($mockup_156, true);

if (!isset(explode(' ', $id)[1])) {
    echo '
        var elt = documentcreateElement("ul")
        elt.appendChild(document.createTextNode("Error, something went wrong. Please try again later."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}

$id = explode(' ', $id)[1];


if (!is_numeric($id) || strlen($id) > 11 || $id < 0) {
    echo '
        var elt = documentcreateElement("ul")
        elt.appendChild(document.createTextNode("Error, this product doesn\'t exist."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}

$GLOBALS['script'] = '';

if (!is_array($json_phone)) {
    $GLOBALS['script'] .= '
        var elt = documentcreateElement("ul")
        elt.appendChild(document.createTextNode("Error, the phone models are not sent in correct format."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (!is_array($json_variants)) {
    $GLOBALS['script'] .= '
        var elt = documentcreateElement("ul")
        elt.appendChild(document.createTextNode("Error, the variants are not sent in correct format."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (!is_array($json_mockups) || !is_array($json_mockups_92)) {
    $GLOBALS['script'] .= '
        var elt = documentcreateElement("ul")
        elt.appendChild(document.createTextNode("Error, the mockups are not sent in correct format."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}


if (!is_numeric($discount)) {
    $GLOBALS['script'] .= '
        document.querySelector("form .input #discount").classList.add("invalid")
        document.querySelector("form .input #discount").nextElementSibling.textContent = "Please fill in this input with a number."
    ';
}else if ($discount < 0) {
    $GLOBALS['script'] .= '
        document.querySelector("form .input #discount").classList.add("invalid")
        document.querySelector("form .input #discount").nextElementSibling.textContent = "This value should be a positive number"
    ';
}

if (!is_numeric($product_price)) {
    $GLOBALS['script'] .= '
        document.querySelector("#product_price").classList.add("invalid")
        document.querySelector("#product_price").nextElementSibling.textContent = "Please fill in this input with a number."
    ';
}else if ($product_price < 0) {
    $GLOBALS['script'] .= '
        document.querySelector("#product_price").classList.add("invalid")
        document.querySelector("#product_price").nextElementSibling.textContent = "This value should be a positive number"
    ';
}

if (empty($product_name) || empty($league) || empty($club)) {
    //Something is empty

    $GLOBALS['script'] .= '

        var inputs_ = [
            document.querySelector("form .input #product_name")
        ]
        inputs_.forEach(x => {
            if (x.value.trim().length === 0) {
                x.classList.add("invalid")
                x.nextElementSibling.textContent = "Please fill in this input."
            }
        })
            
        if (document.querySelector("#league").selectedIndex === 0) {
            document.querySelector("#league").nextElementSibling.classList.add("invalid")
            document.querySelector("#league").parentElement.nextElementSibling.textContent = "Please choose a league and club."
        }
        
        if (document.querySelector("#club").options.length > 1) {
            if (document.querySelector("#club").selectedIndex === 0) {
                var elt = document.createElement("li")
                elt.appendChild(document.createTextNode("You have to choose a club!"))
                document.querySelector(".errorAll").appendChild(elt)
                document.querySelector("#club").nextElementSibling.classList.add("invalid")
                document.querySelector("#club").parentElement.nextElementSibling.textContent = "Please choose a club"
            }
        }else {
            document.querySelector("#league").nextElementSibling.classList.add("invalid")
            document.querySelector("#league").parentElement.nextElementSibling.textContent = "This league might not have any clubs. Please choose a different league or create a club."
        }
    ';
}

if (!is_array($json_phone)) {

    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, the phone model is not sent in correct format. Please reload the page."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}
if (!is_array($json_variants)) {
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, the variants are not sent in correct format. Please reload the page."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (!is_array($json_mockups)) {
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, the mockups are not sent in correct format. Please reload the page."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}
if (!empty($GLOBALS['script'])) {
    echo $GLOBALS['script'];
    die;
}

if (!is_array($json_mockups_92)) {
    
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, the mockups are not sent in correct format. Please reload the page."))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

if (empty(trim($json_mockup_156))) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, you have to add at least a mockup!"))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

if (count($json_mockups_92) !== count($json_mockups)) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("There is some errors with the mockups. Please try again."))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

if (strpos($json_mockup_156, 'png') === false && strpos($json_mockup_156, 'jpg') === false && strpos($json_mockup_156, 'jpeg') === false){
    //Something is wrong with the first mockup!
    echo '
    var mockups_section_card = document.querySelectorAll(".mockups_section .card")
    if (mockups_section_card[0]) {
        mockups_section_card[0].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
        if (mockups_section_card[0].querySelector(".msg")) {
            mockups_section_card[0].querySelector(".msg").textContent = "This mockup doesn\'t have a valid image."
        }
    }
    ';
    die;
}





















if (count($json_phone) === 0) {
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("You have to add at least one phone model to this product."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (count($json_variants) === 0) {
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("You have to add at least one variant to this product."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (count($json_mockups) === 0) {
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("You have to add at least one mockup to this product."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}


if (count($json_mockups_92) === 0) {
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("You have to add at least one mockup to this product."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}


if (count($json_mockups_92) !== count($json_mockups)) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Failed registering all the mockups. Please try again later."))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}


if (((double)$product_price) < 12) {
    //Price is too low
    echo trim('
        document.querySelector("#product_price").classList.add("invalid")
        document.querySelector("#product_price").nextElementSibling.textContent = "The price is too low. Minimum price is $12"
    ');
    die;
}


$index = -1;

foreach ($json_variants as $value) {
    $index++;

    if (!is_array($value)) {
        //Something wrong with the variant
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".variants .card")['.$index.']){
                document.querySelectorAll(".variants .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".variants .card")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this variant. Please delete it!"
            }
        ';
        continue;
    }

    if(!isset($value['name']) || !isset($value['img'])) {
        //Something wrong with the variant
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".variants .card")['.$index.']){
                document.querySelectorAll(".variants .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".variants .card")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this variant. Please delete it!"
            }
        ';
        continue;
    }
    $continue = false;
    if (empty(trim($value['name']))) {
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".variants .card")['.$index.']){
                document.querySelectorAll(".variants .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".variants .card")['.$index.'].querySelector(".msg").textContent = "Give this variant a name."
            }
        ';
        $continue = true;
    }

    if (empty(trim($value['img']))) {
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".variants .card")['.$index.']){
                document.querySelectorAll(".variants .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".variants .card")['.$index.'].querySelector(".msg").textContent = "This variant doesn\'t have an image."
            }
        ';
        $continue = true;
    }
    if ($continue === true) {
        continue;
    }

    if (strpos($value['img'], 'image/') === false) {
        //Uploaded file is invalid
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".variants .card")['.$index.']){
                document.querySelectorAll(".variants .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".variants .card")['.$index.'].querySelector(".msg").textContent = "The uploaded image for this variant is invalid!"
            }
        ';
        $continue = true;
    }

    if (strpos($value['img'], 'png') === false && strpos($value['img'], 'jpg') === false && strpos($value['img'], 'jpeg') === false){
        //Uploaded file is invalid
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".variants .card")['.$index.']){
                document.querySelectorAll(".variants .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".variants .card")['.$index.'].querySelector(".msg").textContent = "The uploaded image for this variant is invalid!"
            }
        ';
        $continue = true;
    }
    if ($continue === true) {
        continue;
    }
}


for ($index = 0; $index < count($json_mockups); $index++) {
    if (empty(trim($json_mockups[$index])) || empty(trim($json_mockups_92[$index]))) {
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".mockups_section .card")['.$index.']){
                document.querySelectorAll(".mockups_section .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".mockups_section .card")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this mockup."
            }
        ';
        continue;
    }

    if (strpos($json_mockups[$index], 'image/') === false || strpos($json_mockups_92[$index], 'image/') === false) {
        //Uploaded file is invalid
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".mockups_section .card")['.$index.']){
                document.querySelectorAll(".mockups_section .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".mockups_section .card")['.$index.'].querySelector(".msg").textContent = "The uploaded file for this mockup is invalid."
            }
        ';
        continue;
    }

    if ((strpos($json_mockups[$index], 'png') === false && strpos($json_mockups[$index], 'jpg') === false && strpos($json_mockups[$index], 'jpeg') === false) || (strpos($json_mockups_92[$index], 'png') === false && strpos($json_mockups_92[$index], 'jpg') === false && strpos($json_mockups_92[$index], 'jpeg') === false)){
        //Uploaded file is invalid
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".mockups_section .card")['.$index.']){
                document.querySelectorAll(".mockups_section .card")['.$index.'].style.boxShadow = "rgb(238, 52, 18) 0px 0px 0px 2px"
                document.querySelectorAll(".mockups_section .card")['.$index.'].querySelector(".msg").textContent = "The uploaded file for this mockup is invalid."
            }
        ';
        continue;
    }
}



//The supported phone models
$allowed_phone_models = [
    "iphone 7/8",
    "iphone 11",
    "iphone x/xr",
    "iphone 12",
    "iphone 12 pro"
];

//Check if the phone models are sent in correct format
$index = -1;
foreach ($json_phone as $phone) {
    $index++;
    $continue = false;
    if (!is_array($phone)) {
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".btn")['.$index.']){
                document.querySelectorAll(".btn")['.$index.'].classList.add("invalid")
                document.querySelectorAll(".btn")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this phone model. Please delete it."
            }
        ';
        $continue = true;
    }
    if ($continue === true) {
        continue;
    }
    if (!isset($phone['device_name']) || !isset($phone['device_id'])) {
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".btn")['.$index.']){
                document.querySelectorAll(".btn")['.$index.'].classList.add("invalid")
                document.querySelectorAll(".btn")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this phone model. Please delete it."
            }
        ';  
    }
    if ($continue === true) {
        continue;
    }
    if (empty($phone['device_name'])) {
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".btn")['.$index.']){
                document.querySelectorAll(".btn")['.$index.'].classList.add("invalid")
                document.querySelectorAll(".btn")['.$index.'].querySelector(".msg").textContent = "Something is wrong with this phone model. Please delete it."
            }
        ';
        $continue = true;
    }
    
    if (empty($phone['device_id'])) {
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".btn")['.$index.']){
                document.querySelectorAll(".btn")['.$index.'].classList.add("invalid")
                document.querySelectorAll(".btn")['.$index.'].querySelector(".msg").textContent = "Please give this device an id."
            }
        ';
        $continue = true;
    }
    if ($continue === true) {
        continue;
    }
    
    $phone_lowercase = trim(strtolower($phone['device_name']));

    if (!in_array($phone_lowercase, $allowed_phone_models)) {
        $GLOBALS['script'] .= '
            if (document.querySelectorAll(".btn")['.$index.']){
                document.querySelectorAll(".btn")['.$index.'].classList.add("invalid")
                document.querySelectorAll(".btn")['.$index.'].querySelector(".msg").textContent = "This phone model is not found!"
            }
        ';
        continue;
    }
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}

//Check if there is a collection

$GLOBALS['collection_id'] = '';

if (!empty($collection)) {
    //Check if the league exist

    $sql = "SELECT * FROM `collections` WHERE `name` = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if (mysqli_stmt_prepare($stmt, $sql)) {
        
        mysqli_stmt_bind_param($stmt, "s", $collection);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $result_check = mysqli_num_rows($result);

        if ($result_check === 0) {
            $GLOBALS['script'] .= '
                if (document.querySelector("#collection")) {
                    document.querySelector("#collection").nextElementSibling.classList.add("invalid")
                    document.querySelector("#collection").parentElement.nextElementSibling.textContent = "The collection you chose doesn\'t exist."
                }
            ';
        }else{
            if ($row = mysqli_fetch_assoc($result)) {
                $GLOBALS['collection_id'] = $row['id'];
            }
        }

    }else{
        $GLOBALS['script'] .= '
            var elt = document.createElement("li")
            elt.appendChild(document.createTextNode("Error, something went wrong."))
            document.querySelector(".errorAll").appendChild(elt)
        ';
    }
}

$sql = "SELECT null FROM `products` WHERE `name` = ? AND `id` != ?";

$stmt = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmt, $sql)) {
    
    mysqli_stmt_bind_param($stmt, 'si', $product_name, $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $result_check = mysqli_num_rows($result);
    
    if ($result_check > 0) {
        //A product with the same name exist

        $GLOBALS['script'] .= '
            document.querySelector("#product_name").classList.add("invalid")
            document.querySelector("#product_name").nextElementSibling.textContent = "A product with the same name exists. Please change the name."
        ';
    }
}else{
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, something went wrong."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

//Cheking if the league exist
$GLOBALS['league_id'] = null;

$sql = "SELECT `id` FROM `leagues` WHERE `name` = ?";

$stmt = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmt, $sql)) {
    
    mysqli_stmt_bind_param($stmt, 's', $league);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $result_check = mysqli_num_rows($result);
    
    if ($result_check === 0) {
        //A product with the same name exist
        $GLOBALS['script'] .= '
            document.querySelector("#league").nextElementSibling.classList.add("invalid")
            document.querySelector("#league").parentElement.nextElementSibling.textContent = "The league you have chosen does not exist. Please change the league, or reload the page."
        ';
    }else {
        if ($row = mysqli_fetch_assoc($result)) {
            $GLOBALS['league_id'] = $row['id'];
        }
    }
}else{
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, something went wrong."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}

if (!isset($GLOBALS['league_id'])) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, something went wrong."))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

//Cheking if the club exist
$GLOBALS['club_id'] = null;

$sql = "SELECT * FROM `clubs` WHERE `name` = ? AND `league_id` = ?";

$stmt = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmt, $sql)) {
    
    mysqli_stmt_bind_param($stmt, 'si', $club, $GLOBALS['league_id']);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $result_check = mysqli_num_rows($result);
    
    if ($result_check === 0) {
        //A product with the same name exist

        $GLOBALS['script'] .= '
            document.querySelector("#club").nextElementSibling.classList.add("invalid")
            document.querySelector("#club").parentElement.nextElementSibling.textContent = "The club you have chosen either does not exist, or does not match with the league."
        ';
    }else {
        if ($row = mysqli_fetch_assoc($result)) {
            $GLOBALS['club_id'] = $row['id'];
        }
    }
}else{
    $GLOBALS['script'] .= '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, something went wrong."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
}

if (!preg_match('/^[a-zA-Z0-9\/ ]*$/i', $product_name)) {

    $GLOBALS['script'] .= '
        document.querySelector("#product_name").classList.add("invalid")
        document.querySelector("#product_name").nextElementSibling.textContent = "Invalid name! Please write a valid name."
    ';
}

if (!empty($GLOBALS['script'])) {
    echo trim($GLOBALS['script']);
    die;
}



/*
Reset
*/

$sql = "SELECT `file` FROM `products` WHERE `id` = ?";

$stmt = mysqli_stmt_init($conn);

$stmt->prepare($sql) or die('Something went Wrong! Please try again.');

mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$result_check = mysqli_num_rows($result);

if ($result_check === 0) {

    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Product not found. Please try again."))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

if ($row = mysqli_fetch_assoc($result)) {
    $GLOBALS['old_file_name'] = $row['file'];
}


if (!isset($GLOBALS['old_file_name']) || empty($GLOBALS['old_file_name'])) {
    echo trim('
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Something Went wrong! Please try again."))
        document.querySelector(".errorAll").appendChild(elt)
    ');
    die;
}

$file_path = '../../../product_details/' . $GLOBALS['old_file_name'];

$file = fopen($file_path, 'r') or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Unable to connect to the server!"))
    document.querySelector(".errorAll").appendChild(elt)
');
$file_txt = fread($file, filesize($file_path));
fclose($file);


$file_details = json_decode($file_txt, 1);

if (!isset($file_details['details']['name']) || !isset($file_details['details']['price']) || !isset($file_details['details']['collection_id']) || !isset($file_details['details']['club_id']) || !isset($file_details['details']['league_id']) || !isset($file_details['details']['product_id']) || !isset($file_details['details']['discount'])) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Something is wrong with this product. Please remove it and re add it."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}

if(empty($file_details['details']["league_id"]) || empty($file_details['details']["club_id"]) || empty($file_details['details']["product_id"])) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Something is wrong with this product. Please remove it and re add it."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}

$print = $file_details['print_files'];
$mockups = $file_details['mockups'];

if (!is_array($print) || !is_array($mockups)) {
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("There is something wrong with this product. Please delete it or try again."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}

foreach ($print as $p) {
    if (file_exists("../../../assets/print/" . $p['name'])) {
        unlink("../../../assets/print/" . $p['name']);
    }
}

foreach ($mockups as $m) {
    if (file_exists("../../../assets/mockups/92/" . $m)) {
        unlink("../../../assets/mockups/92/" . $m);
    }
    if (file_exists("../../../assets/mockups/156/" . $m)) {
        unlink("../../../assets/mockups/156/" . $m);
    }
    if (file_exists("../../../assets/mockups/350/" . $m)) {
        unlink("../../../assets/mockups/350/" . $m);
    }
}



$sql = "INSERT INTO `products` (`name`, `price`, `club_id`, `league_id`, `collection_id`, `file`) VALUES(?, ?, ?, ?, ?, ?)";

$sql = "UPDATE `products` SET
    `name` = ?,
    `file` = ?,
    `price` = ?,
    `club_id` = ?,
    `league_id` = ?,
    `collection_id` = ?
    WHERE `id` = ?
";


$stmt = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmt, $sql)) {

    $file_name = str_replace(' ', '-', $product_name);
    $file_name = str_replace('/', '_', $file_name);
    $file_name .= '.json';

    mysqli_stmt_bind_param($stmt, 'ssiiiii', $product_name, $file_name, $product_price, $GLOBALS['club_id'], $GLOBALS['league_id'], $GLOBALS['collection_id'], $id);

    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) === 1) {

        //Update the file name
        
        $sql = "UPDATE `products` SET `file` = ? WHERE `id`= ?;";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {


            $GLOBALS['file_name'] = str_replace(' ', '-', $product_name);
            $GLOBALS['file_name'] = str_replace('/', '_', $GLOBALS['file_name']);
            $GLOBALS['file_name'] .= '-'.$id.'.json';
            

            mysqli_stmt_bind_param($stmt, 'si', $GLOBALS['file_name'], $id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 0) {
                echo '
                    var elt = document.createElement("li")
                    elt.appendChild(document.createTextNode("Error, couldn\'t set some values. Please try again."))
                    document.querySelector(".errorAll").appendChild(elt)
                ';
                die;
            }

        }else {
            echo '
                var elt = document.createElement("li")
                elt.appendChild(document.createTextNode("Error, something went wrong. Please try again later."))
                document.querySelector(".errorAll").appendChild(elt)
            ';
            die;
        }

    }else {
        echo '
            var elt = document.createElement("li")
            elt.appendChild(document.createTextNode("Failed to update Product. Please try again later."))
            document.querySelector(".errorAll").appendChild(elt)
        ';
        die;
    }


}else {
    //Error in the query
    echo '
        var elt = document.createElement("li")
        elt.appendChild(document.createTextNode("Error, something went wrong. Please try again later."))
        document.querySelector(".errorAll").appendChild(elt)
    ';
    die;
}



$new_json_variants = [];

foreach ($json_variants as $value) {


    $imgData = str_replace(' ','+',$value['img']);
    $imgData =  substr($imgData,strpos($imgData,",")+1);
    $imgData = base64_decode($imgData);
    // Path where the image is going to be saved
    $filePath = '../../../assets/print/' . $value['name'] . '-' . $id .'.png';
    // Write $imgData into the image file
    $file = fopen($filePath, 'w');
    fwrite($file, $imgData);
    fclose($file);    

    array_push($new_json_variants, [
        'img' => $value['name'] . '-' . $id .'.png',
        'name' => $value['name']
    ]);

}

$index = 0;
$new_json_mockups = [];





foreach ($json_mockups as $value) {

    if ($index === 0) {
        $product_name_new = str_replace(' ', '-', $product_name);
        $product_name_new = str_replace('/', '_', $product_name_new);
    
        $imgData = str_replace(' ','+', $json_mockup_156);
        $imgData =  substr($imgData,strpos($imgData,",")+1);
        $imgData = base64_decode($imgData);
        // Path where the image is going to be saved
        $filePath = '../../../assets/mockups/156/'. $product_name_new . "-" .$index. "-" .$id .'.png';
        // Write $imgData into the image file
        $file = fopen($filePath, 'w');
        fwrite($file, $imgData);
        fclose($file);
    }

    $product_name_new = str_replace(' ', '-', $product_name);
    $product_name_new = str_replace('/', '_', $product_name_new);

    $imgData = str_replace(' ','+', $value);
    $imgData =  substr($imgData,strpos($imgData,",")+1);
    $imgData = base64_decode($imgData);
    // Path where the image is going to be saved
    $filePath = '../../../assets/mockups/350/'. $product_name_new . '-' . $index . "-" .$id .'.png';
    // Write $imgData into the image file
    $file = fopen($filePath, 'w');
    fwrite($file, $imgData);
    fclose($file);

    array_push($new_json_mockups, $product_name_new . '-' . $index . "-" .$id .'.png');
    
    $index++;
}

$i = 0;

foreach ($json_mockups_92 as $value) {

    $product_name_new = str_replace(' ', '-', $product_name);
    $product_name_new = str_replace('/', '_', $product_name_new);

    $imgData = str_replace(' ','+', $value);
    $imgData =  substr($imgData,strpos($imgData,",")+1);
    $imgData = base64_decode($imgData);
    // Path where the image is going to be saved
    $filePath = '../../../assets/mockups/92/'. $product_name_new . '-' . $i . "-" .$id .'.png';
    // Write $imgData into the image file
    $file = fopen($filePath, 'w');
    fwrite($file, $imgData);
    fclose($file);
    $i++;
}

$json = [
    "details" => [
        "name" => $product_name,
        "price" => $product_price,
        "collection_id" => $GLOBALS['collection_id'],
        "club_id" => $GLOBALS['club_id'],
        "league_id" => $GLOBALS['league_id'],
        "product_id" => $id,
        "discount" => $discount
    ],
    "items" => $json_phone,
    "print_files" => $new_json_variants,
    "mockups" => $new_json_mockups,
];

$file = fopen('../../../product_details/' . $GLOBALS['file_name'], "w") or die('
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Unable to generate fil!"))
    document.querySelector(".errorAll").appendChild(elt)
');
$txt = json_encode($json);
fwrite($file, $txt);
fclose($file);

echo '
    var elt = document.createElement("li")
    elt.appendChild(document.createTextNode("Product updated successfully."))
    document.querySelector(".successAll").appendChild(elt)
';
die;