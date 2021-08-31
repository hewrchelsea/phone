<?php
$app->get('/', function ($request, $response, $args) {
?>

    <div class="location">HOME / SHOP</div>

    <?php
    //Get the number of results
    require_once "../conn/conn.php";

    $sql = "SELECT null FROM `products` WHERE 1";
    $result = mysqli_query($GLOBALS['conn'], $sql);
    $result_check = mysqli_num_rows($result);
    $total = 0;
    if (is_numeric($result_check)) {
        $total = $result_check;
    }
    
    ?>
    <head>
        <link rel="stylesheet" href="/phone/style/style.css">
        <script src="/phone/js/main.js"></script>
    </head>
    <div class="listing_header">
        <div class="result_counter">
            <?php
            if ($total >= 10) {
                echo '10 Of' . $total;
            } else if ($total == 0) {
                echo '0 Of 0';
            }else {
                echo $total . " Of " . $total;
            }
            ?>
        </div>
        <div class="filter">
            <p>Filter</p>
            <img src="/phone/images/icons/arrow.png" alt="" class="icon">
        </div>
    </div>
    <div class="listings">


    <?php
        $sql = "SELECT * FROM `products` LIMIT 0, 100";
        $result = mysqli_query($GLOBALS['conn'], $sql);
        $result_check = mysqli_num_rows($result);

        if ($result_check != 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                echo '<a href="/phone/shop/products/'.str_replace('.json', '', $row['file']).'">
                    <div class="card product">
                        <img src="/phone/assets/mockups/156/'.str_replace('/', '_', str_replace(' ', '-', $row['name'])) . '-0-' . $row['id'] .'.png" class="product_img">
                        <h7 class="product_title">'.$row['name'].'</h7>
                        <div class="product_price">$'.$row['price'].'</div>
                    </div>
                </a>';
            }

        } else {
            //No results
            echo "<p class='nothing'>Nothing to show</p>";
        }
    ?>
    </div>
    <a href="#" class="backToTop"><img src="/phone/images/icons/arrow.png"></a>
    <div class="filters_popup">

        <div class="filters">
            <div class="header">
            <div class="left">
                <div class="closeBtn">×</div>
            </div>
            <div class="right">
                <h1 class="title">Filter</h1>
                <div class="resetBtn">Reset</div>
            </div>
            </div>
            <div class="filter_section filter_price">
                <h2 class="title">Price</h2>
                <div class="buttons">
                    <a href="#" class="byDefault active">Default</a>
                    <a href="#" class="highToLow">High To Low</a>
                    <a href="#" class="lowToHigh">Low To High</a>
                </div>
            </div>
            <div class="filter_section filter_leagues">
                <h2 class="title">League</h2>
                <div class="buttons">
                    <?php
                            $sql = "SELECT * FROM `leagues`";
                        $result = mysqli_query($GLOBALS['conn'], $sql);
                        $result_check = mysqli_num_rows($result);
                    
                        if ($result_check == 0) {
                            echo "
                                <script>
                                    document.querySelector('.filter_leagues')?.remove()
                                </script>
                            ";
                        }else {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<a href='#' class='league'>".$row['name']."</a>";
                            }
                        }
                    ?>
                </div>
            </div>
    
        </div>
    </div>
<?php

});
?>
