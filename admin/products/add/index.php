<?php
session_start();

if (!isset($_SESSION['admin'])) {
    http_response_code(404);
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <script src="../../../js/jquery.js"></script>
    <script src="./main.js" defer></script>
    <title>FOOTYPHONES | ADMIN | PRODUCTS | ADD</title>
</head>
<body>
    <h1 class="title">Manage Clubs</h1>
    <form action="./" method="POST" autocomplete="off">
        <div class="input">
            <label for="product_name">Product name</label>
            <input type="text" id="product_name">
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="product_price">Product Price</label>
            <input type="text" id="product_price">
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="discount">Discount</label>
            <input type="text" id="discount">
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="league">League</label>
            <div class="select_parent">
                    <?php
                    
                    require_once "../../../conn/conn.php";

                    $conn = $GLOBALS['conn'];

                    $sql = "SELECT `name` FROM `leagues` WHERE 1";
                    $result = mysqli_query($conn, $sql);

                    $result_check = mysqli_num_rows($result);

                    if ($result_check === 0) {
                        ?>
                <select id="league">
                    <option value="" disabled selected>Not chosen</option>
                </select>
                <div class="custom_select invalid">Not Chosen</div>
            </div>
            <p class="error" style="display:block">Error finding leagues. Please create a leage first at: <a href="/phone/admin/leagues">admin/leagues</a></p>
        </div>
                        <?php
                    }else {

                        ?>
                <select id="league">
                    <option value="" selected disabled>Not Chosen</option>
                    <?php
                        while($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <option value="<?php echo $row['name'];?>"><?php echo $row['name'];?></option>
                            <?php
                        }
                    ?>
                </select>
                <div class="custom_select">Not Chosen</div>
            </div>
            <p class="error"></p>
        </div>
                        
                        <?php
                        
                    }

                    
                    ?>
        <div class="input club_input" style="display:none">
            <label for="club">Club</label>
            <div class="select_parent">
                <select id="club">
                    <option value="0" disabled selected>Not Chosen</option>      
                </select>
                <div class="custom_select">Not Chosen</div>
            </div>
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="collection">Collection (Not Required)</label>
            <div class="select_parent">
                <select id="collection">
                    <option value="" selected>Not Chosen</option>
                    <?php
                    $sql = "SELECT `name` FROM `collections` WHERE 1";
                    $result = mysqli_query($conn, $sql);
                    $result_check = mysqli_num_rows($result);

                    if ($result_check > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <option><?php echo $row['name']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                <div class="custom_select">Not Chosen</div>
            </div>
            <p class="error"></p>
        </div>

        <div class="addBtn addPhone" data-open="phone_list">
            Add Phone Model
            <div class="icon">
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="phone_list">
            <div class="input">
                <label>Phone Model</label>
                <div class="select_parent">
                    <select id="variant_select">
                        <option value="0" disabled selected>Not Chosen</option>      
                        <option value="1">Iphone 7/8</option>      
                        <option value="2">Iphone 11</option>      
                        <option value="3">Iphone x/xr</option>      
                        <option value="4">Iphone 12</option>
                        <option value="5">Iphone 12 Pro</option>
                    </select>
                    <div class="custom_select">Not Chosen</div>
                </div>
                <p class="error"></p>
            </div>
            <div class="input">
                <label for="device_id">Device Id</label>
                <input type="text" id="device_id">
                <p class="error"></p>
            </div>
            <button type="button" class="addPhone">Add Phone Model</button>
        </div>
        <div class="addBtn addVariant" data-open="variant">
            Add Variant
            <div class="icon">
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="variant">
            <div class="input">
                <label for="player_name">Player Name</label>
                <input type="text" id="player_name">
                <p class="error"></p>
            </div>
            <div class="upload">
                <label for="input_file">Print File</label>
                <div class="custom_input_file_parent">
                    <input type="file" id="input_file">
                    <div class="custom_input_file">Choose File</div>
                </div>
                <p class="error"></p>
            </div>
            <div class="preview" style="">
                <button type="button" class="deleteBtn cross">×</button>
                <img src="//" alt="Print file">
                <h3 class="image_title"></h3>
            </div>
            <button type="button" class="addVariant">Add Variant</button>
        </div>
        <div class="addBtn addMockup" data-open="mockups">
            Add Mockup
            <div class="icon">
                <div></div>
                <div></div>
            </div>
        </div>
        <div class="mockups">
            <div class="upload_mockup">
                <label for="input_file">Mockup File</label>
                <div class="custom_input_file_parent">
                    <input type="file" id="input_file">
                    <div class="custom_input_file">Choose File</div>
                </div>
                <p class="error"></p>
            </div>
            <div class="preview_mockup" style="">
                    <button type="button" class="deleteBtn cross">×</button>
                    <img src="//" alt="Print file">
                    <h3 class="image_title"></h3>
            </div>
            <button type="button" class="addMockup_btn">Add Mockup</button>
        </div>
        <div class="phones">
            <h3 class="phones_title">Added Phone Models:</h3>
        </div>
        <div class="variants">
            <h3 class="variants_title">Added Variants:</h3>
        </div>
        <div class="mockups_section">
            <h3 class="mockups_section_title">Added Mockups:</h3>
        </div>
        <ul class="errorAll"></ul>
        <ul class="successAll"></ul>

        <button class="addProduct">Add Product</button>
    </form>
    <div class="loading"></div>
</body>
</html>
