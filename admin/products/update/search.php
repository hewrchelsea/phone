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

if (!isset($_POST['searchBy']) || !isset($_POST['name']) || !isset($_POST['clicked'])) {
    http_response_code(404);
    die;
}

require_once "../../../conn/conn.php";
$conn = $GLOBALS['conn'];

$searchBy = $_POST['searchBy'];
$clicked = $_POST['clicked'];
$name = mysqli_real_escape_string($conn, trim($_POST['name']));


if (empty($searchBy)) {
    http_response_code(404);
    die;
}

if ($searchBy != 'name' && $searchBy != 'id') {
    http_response_code(404);
    die;
}

if (empty($clicked)) {
    http_response_code(404);
    die;
}
if ($clicked != 'clicked' && $clicked != 'notClicked') {
    http_response_code(404);
    die;
}

if (empty($name)) {
    if ($clicked == 'clicked') {
        echo "<p class='msg'>Please fill in the input.</p>";
        echo '
            <script type="text/javascript">
                document.querySelector(".results").style.display = "flex"
            </script>
        ';
    }else {
        echo '
            <script type="text/javascript">
                document.querySelector(".results").style.display = "none"
            </script>
        ';
    }
    die;
}

echo '
<script type="text/javascript">
    document.querySelector(".results").style.display = "flex"
</script>
';


if ($searchBy == 'name') {


    $sql = "SELECT * FROM `products` WHERE `name` LIKE ? ESCAPE '*' LIMIT 0, 5;";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        $name .= '%';
        $newName = '*' . $name;
        mysqli_stmt_bind_param($stmt, 's', $newName);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $result_check = mysqli_num_rows($result);
        if ($result_check === 0) {
            echo "<p class='msg'>No product found with the given name.</p>";
            die;
        }
        $index = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="item" onclick="loadData(this)">
                <p class="item-name">'.$row['name'].'</p>
                <p class="item-id">Id: '.$row['id'].'</p>
            </div>
            ';
            if ($index > 5) {
                break;
            }
            $index++;
        }
    }


}else if ($searchBy == 'id') {

    $sql = "SELECT * FROM `products` WHERE `id` LIKE ? ESCAPE '*' LIMIT 0, 5;";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        $name .= '%';
        $newName = '*' . $name;
        mysqli_stmt_bind_param($stmt, 's', $newName);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $result_check = mysqli_num_rows($result);
        if ($result_check === 0) {
            echo "<p class='msg'>No product found with the given id.</p>";
            die;
        }
        $index = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            echo '
            <div class="item" onclick="loadData(this)">
                <p class="item-name">'.$row['name'].'</p>
                <p class="item-id">Id: '.$row['id'].'</p>
            </div>
            ';
            if ($index > 5) {
                break;
            }
            $index++;
        }
    }
    
}else {
    http_response_code(404);
    die;
}

