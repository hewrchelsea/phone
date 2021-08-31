<?php



$GLOBALS['josn_array'] = Array();


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
    if (strpos($_SERVER['HTTP_REFERER'], '/shop/cart') <= 0){
        http_response_code(404);
        die;
    }
}


if (!isset($_POST['data']) || !isset($_POST['method'])) {
    http_response_code(404);
    die;
}


if ($_POST['method'] != 'check' && $_POST['method'] != 'return') {
    http_response_code(404);
    die;
}

$data_json = json_decode($_POST['data'], 1);

if (empty($data_json)) {
    //No data sent by the request maker
    echo "Error we recieved no data";
    http_response_code(404);
    die;
}

if (!is_array($data_json)) {
    //The data sent is not an array
    echo "Error we recieved no array.";
    http_response_code(404);
    die;
}


if (count($data_json) < 1) {
    //No item in the cart
    echo "Error, there is no item in the cart";
    http_response_code(404);
    die;
}



foreach ($data_json as $value) {

    if (!is_array($value)) {
        echo "Error, something is wrong with the cart.";
        http_response_code(404);
        die;
    }

    if (count($value) != 5) {
        echo "Error, something is wrong with the cart items.";
        http_response_code(404);
        die;
    }
    if (!isset($value['product_name']) || !isset($value['device']) || !isset($value['player']) || !isset($value['qty'])) {
        echo "Error, something is wrong with the cart items.";
        http_response_code(404);
        die;
    }

    if (!isset($value['device']['name']) || !isset($value['device']['id'])) {
        echo "Device is not specified";
        http_response_code(404);
        die;
    }
    //We have all the data
    require_once "../conn/conn.php";

    $product_name = trim($value['product_name']);
    $player = trim($value['player']);
    $qty = trim($value['qty']);
    $device_name = trim($value['device']['name']);
    $device_id = trim($value['device']['id']);

    if (!file_exists("../product_details/" .$product_name . ".json")) {
        continue;
    }

    if ($qty < 1) {
        continue;
    }
    $file = fopen("../product_details/" .$product_name . ".json", 'r') or die('Unable to connect to the server!');
    $file_txt = fread($file, filesize("../product_details/" .$product_name . ".json"));
    fclose($file);

    $file_details = json_decode($file_txt, 1);
    // print_r($file_details);
    // echo $file_details['details']['name'];
    
    $player_check = FALSE;
    $device_check = FALSE;



    if ($player != 'none') {
        foreach ($file_details['print_files'] as $val) {
            if ($player != $val['name']) {
                continue 1;
            }
            $player_check = TRUE;
        }
    }else {
        if (count($file_details['print_files']) == 0) {
            $player_check = TRUE;
        }
    }


    foreach ($file_details['items'] as $val) {

        if ($device_name != $val['device_name'] || $device_id != $val['device_id']) {
            continue 1;
        }

        $device_check = TRUE;
    }


    if (!$player_check || !$device_check) {
        http_response_code(404);
        die;
    }
    

    if ($_POST['method'] == 'check') {
        echo "TRUE";
    }else {
        

        $product_name = $file_details['details']['name'];
        $player = trim($value['player']);
        $qty = trim($value['qty']);
        $device_name = trim($value['device']['name']);
        $device_id = trim($value['device']['id']);
        $price = $file_details['details']['price'];
        $img_src = $file_details['mockups'][0];


        $array = Array(
            'product_name' => $product_name,
            'player' => $player,
            'device_name' => $device_name,
            'price' => $price,
            'qty' => $qty,
            'img_src' => $img_src 
        );
        array_push($GLOBALS['josn_array'], json_encode($array, JSON_UNESCAPED_SLASHES));
    }
    
}

echo json_encode($GLOBALS['josn_array'], JSON_UNESCAPED_SLASHES);

?>