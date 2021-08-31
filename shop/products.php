<?php


$app->get('/products/{product_id}[/]', function ($request, $response, $args) {
    
    $product_id = mysqli_real_escape_string($GLOBALS['conn'], $args['product_id']);
    $product_id .= ".json";

    $sql = "SELECT * FROM `products` WHERE `file` = ?;";
    $stmt = mysqli_stmt_init($GLOBALS['conn']);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $product_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $result_check = mysqli_num_rows($result);

        if ($result_check == 0) {
            die();
        }
        $myfile = fopen("../product_details/" . $product_id, "r") or die("Unable to load data!");
        $data = fread($myfile,filesize("../product_details/" . $product_id));
        fclose($myfile);
        
        $array = json_decode($data, true);
        $GLOBALS['product_name'] = $array['details']['name'];
        $GLOBALS['product_price'] = $array['details']['price'];
        $GLOBALS['images'] = $array['mockups'];
        $GLOBALS['product_print_file'] = $array['print_files'];
        $GLOBALS['product_collection_id'] = $array['details']['collection_id'];
        $GLOBALS['product_club_id'] = $array['details']['club_id'];
        $GLOBALS['product_league_id'] = $array['details']['league_id'];

        
        //Items array
        $GLOBALS['items'] = $array['items'];
    }


    ?>
    
    <head>
        <link rel="stylesheet" href="/phone/style/products.css">
        <script src="/phone/js/products.js" defer></script>
    </head>

    <div class="products_section">
        <img src="/phone/assets/mockups/350/<?php echo $GLOBALS['images'][0];?>" alt="case image">
        <div class="other_images">
            <?php
            $counter = 0;
                foreach($GLOBALS['images'] as $img){
                    if ($counter === 0) {
                        echo "<img src=\"/phone/assets/mockups/92/".$img."\"alt=\"Product Images\" class=\"chosen\" data-src=\"/phone/assets/mockups/350/".$img."\">";

                    }else {
                        echo "<img src=\"/phone/assets/mockups/92/".$img."\"alt=\"Product Images\" data-src=\"/phone/assets/mockups/350/".$img."\">";
                    }
                    $counter++;
                }
            ?>
        </div>
        <h1 class="product_name"><?php echo $GLOBALS['product_name'];?></h1>
        <div class="product_price_big">$<?php echo $GLOBALS['product_price'];?> USD</div>
        
        <div class="input">
            <label>Phone Model</label>
            <div class="select_parent">
                <select id="phone_model">
                    <option value="0" disabled selected>Not Chosen</option>
                    <?php
                    
                    foreach ($GLOBALS['items'] as $value) {
                        ?>
                        <option value="<?php echo $value['device_id'];?>"><?php echo $value['device_name'];?></option>
                        <?php
                    }
                    
                    ?>
                </select>
                <div class="custom_select">Not Chosen</div>
            </div>
            <p class="error phonemodel_error">
                This field is required
            </p>
        </div>
            
            <div class="input">
                <label>Player</label>
                <div class="select_parent">
                
                <select id="player">
                    <option value="0" disabled selected>Not Chosen</option>
                    <?php
                        foreach($GLOBALS['product_print_file'] as $list) {
                            ?>
                            <option value=""><?php echo $list['name'];?></option>
                            <?php
                        }
                    
                    ?>
                </select>
                <div class="custom_select">Not Chosen</div>
                </div>
                <p class="error player_error">
                    This field is required
                </p>
            </div>

        <button type="submit" class="add_to_cart">ADD TO CART</button>
        <div class="description">
        
            <h2 class="description_title">Item description</h2>
            <p class="description_txt">
            Protect your phone and the environment all in one go! This Biodegradable iPhone Case fully decomposes in ~1 year in a warm, moist, and microorganism-friendly environment. Protect your phone from bumps and scratches in style.</p>
            <ul class="features">
                <li>100% biodegradable material</li>
                <li>Components: soil (30%), onions (7.5%), carrots (7.5%), pepper (7.5%), sawdust (1.5%), rice (18%), soybeans (18%), wheat (10%)</li>
                <li>Thickness over 1.8mm</li>
                <li>Anti-shock protection</li>
                <li>Durability: 1–2 years</li>
                <li>Decomposes in ~1 year</li>
                <li>Packaged in a degradable and protective CPE 07 bag and shipped in a carton box</li>
            </ul>
        </div>

    </div>
    <div class="related">
        <h2 class="related_title">You may also like:</h2>
        <div class="listings">
        <a href="#">
            <div class="card product">
                <img src="/phone/images/product.png" class="product_img">
                <h7 class="product_title">Chelsea Kit Case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>
        <a href="#">
            <div class="card product">
                <img src="/phone/images/product.png" class="product_img">
                <h7 class="product_title">Chelsea Kit Case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>
        <a href="#">
            <div class="card product">
                <img src="/phone/images/product.png" class="product_img">
                <h7 class="product_title">Chelsea Kit Case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>
        <a href="#">
            <div class="card product">
                <img src="/phone/images/product.png" class="product_img">
                <h7 class="product_title">Chelsea Kit Case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>

        </div>

    </div>

    
    
    <?php


});

?>




