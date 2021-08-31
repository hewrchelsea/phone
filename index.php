<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <script src="./js/main.js"></script>
    <title>FOOTYPHONES | SHOP</title>
</head>
<body>
    <header>
        <img src="./images/icons/logo.png" alt="FOOTYPHONES" class="logo">
        <div class="right">
            <a href="javascript:void(0)" class="search" title=""><img src="./images/icons/search.png"></a>
            <a href="javascript:void(0)" class="cart" title=""><img src="./images/icons/cart.png"></a>
            <div class="burger">
                <div></div>
                <div></div>
            </div>
            <div class="nav_parent">
                <div class="nav">
                    <a href="#">HOME</a>
                    <a href="#">ACCOUNT</a>
                    <a href="#">ABOUT US</a>
                    <p class="copy">© 2021. ALL RIGHTS RESERVED. FOOTYPHONES.</p>
                </div>
            </div>
        </div>
    </header>
    <div class="location">HOME / SHOP</div>

    <div class="listing_header">
        <div class="result_counter">10 Of 2103</div>
        <div class="filter">
            <p>Filter</p>
            <img src="./images/icons/arrow.png" alt="" class="icon">
        </div>
    </div>
    <div class="listings">
        <a href="#">
            <div class="card product">
                <img src="./images/product.png" class="product_img">
                <h7 class="product_title">Chelsea Kit Case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>


        <a href="#">
            <div class="card product">
                <img src="./images/product1.png" class="product_img">
                <h7 class="product_title">Tottenham kit case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>
        
        <a href="#">
            <div class="card product">
                <img src="./images/product2.png" class="product_img">
                <h7 class="product_title">Liverpool kit case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>

        
        <a href="#">
            <div class="card product">
                <img src="./images/product3.png" class="product_img">
                <h7 class="product_title">It's a Chelsea thing case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>



        <a href="#">
            <div class="card product">
                <img src="./images/product4.png" class="product_img">
                <h7 class="product_title">Manchester United kit case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>

        <a href="#">
            <div class="card product">
                <img src="./images/product5.png" class="product_img">
                <h7 class="product_title">Manchester city kit case</h7>
                <div class="product_price">$14.99</div>
            </div>
        </a>
    </div>
    <a href="#" class="backToTop"><img src="./images/icons/arrow.png"></a>
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
                        require_once "./conn/conn.php";
                        $sql = "SELECT * FROM `leagues`";
                        $result = mysqli_query($conn, $sql);
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


    <footer>
        <h2 class="title">Navigation</h2>
        <a href="#">Privacy Policy</a>
        <a href="#">Refund Policy</a>
        <a href="#">Terms & Conditions</a>
        <a href="#">Shipping & Returns</a>
        <a href="#">FAQ</a>
        <a href="#">Contact Us</a>
        <p class="copy">© 2021. ALL RIGHTS RESERVED. FOOTYPHONES.</p>

    </footer>


</body>
</html>