<?php

$app->get('/leagues/{league_name}[/]', function ($request, $response, $args) {
    $league_name = mysqli_real_escape_string($GLOBALS['conn'], $args['league_name']);

    $league_name = str_replace('-', ' ', $league_name);


    $sql = "SELECT * FROM `leagues` WHERE `name` = ?";

    $stmt = mysqli_stmt_init($GLOBALS['conn']);

    if (mysqli_stmt_prepare($stmt, $sql)) {

        mysqli_stmt_bind_param($stmt, 's', $league_name);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $result_check = mysqli_num_rows($result);


        if ($result_check == 0) {
            http_response_code(404);
            die();
        }
        if ($row = mysqli_fetch_assoc($result)) {
           ?>
            <div class="profile_parent">
                <div class="profile">
                    <div class="left">
                        <img src="/phone/images/leagues/<?php echo $row['img']; ?>" alt="<?php echo $row['name']; ?>" class="league_logo">
                    </div>
                    <div class="right">
                    <p class="description">Subscribe to see get notified whenever we add some new cases to this league.</p>
                        <label for="input">Enter Your Email</label>
                        <input type="text" id="input">
                        <button type="submit">Subscribe</button>
                    </div>
                    <div class="closeBtn">×</div>
                </div>
            </div>
           <?php 
        ?>


    <head>
        <link rel="stylesheet" href="/phone/style/league.css">
        <script src="/phone/js/leagues.js" defer></script>
    </head>

    <div class="location">HOME / SHOP</div>
    <div class="listing_header">
    <?php
            $id = $row['id'];
            $sql = "SELECT * FROM `products` WHERE `league_id` = '" . $id . "';";
            $result = mysqli_query($GLOBALS['conn'], $sql);
            $result_check = mysqli_num_rows($result);
            if ($result_check > 20) {
                ?>
                    <div class="result_counter">20 Of <?php echo $result_check; ?></div>
                <?php
            }else {
                ?>
                    <div class="result_counter"><?php echo $result_check; ?> Of <?php echo $result_check; ?></div>
                <?php
            }
        }

        ?>
        <div class="filter">
            <p>Filter</p>
            <img src="/phone/images/icons/arrow.png" alt="" class="icon">
        </div>
    </div>
    <div class="listings">

    <?php
        if ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $sql = "SELECT * FROM `products` WHERE `league_id` = '" . $id . "' LIMIT 20;";
            $result = mysqli_query($GLOBALS['conn'], $sql);
            $result_check = mysqli_num_rows($result);
            while ($row = mysqli_fetch_assoc($result)) {
                ?>

            <div class="card product">
                <img src="/phone/images/products/<?php echo $row['img'];?>" class="product_img">
                <h7 class="product_title"><?php echo $row['name'];?></h7>
                <div class="product_price">$<?php echo $row['price'];?></div>
            </div>


                <?php
            }
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
        </div>
    </div>




        <?php

    }else {
        echo "Something went wrong.";
        die();
    }

});

?>