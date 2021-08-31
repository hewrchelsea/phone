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

if (!isset($_POST['name']) || !isset($_POST['id'])) {
    http_response_code(404);
    die;
}

require_once "../../../conn/conn.php";
$conn = $GLOBALS['conn'];

$name = mysqli_real_escape_string($conn, trim($_POST['name']));
$id = mysqli_real_escape_string($conn, trim($_POST['id']));


if (empty($name) || empty($id)) {
    echo "<p class=\"errorText\">Something is wrong with the chosen product. Please try again.</p>";
    die;
}

if (!isset(explode(' ', $id)[1])) {
    echo "<p class=\"errorText\">Error, something went wrong. Please try again later.</p>";
    die;
}

$id = explode(' ', $id)[1];

if (!is_numeric($id)) {
    echo "<p class=\"errorText\">The given id is not a number. Please try again later.</p>";
    die;
}

//Validate the product name

if (strlen($id) > 11) {
    echo "<p class=\"errorText\">Product not found. Please try a different product.</p>";
    die;
}
if ($id < 0) {
    echo "<p class=\"errorText\">Product not found. Please try a different product.</p>";
    die;
}

//Check if product exist

$sql = "SELECT * FROM `products` WHERE `name` = ? AND `id` = ?;";

$stmt = mysqli_stmt_init($conn);

$stmt->prepare($sql) or die("<p class=\"errorText\">Error in the query</p>");

mysqli_stmt_bind_param($stmt, 'si', $name, $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    echo "<p class=\"errorText\">Product not found. Please try a different product.</p>";
    die;
}

$row = mysqli_fetch_assoc($result) or die("<p class=\"errorText\">Error, something went wrong. Please try again later.</p>");

$file_path = "../../../product_details/" . $row['file'];

if (!file_exists($file_path)) {
    die("<p class=\"errorText\">Unable to connect to the server!</p>");
}
$file = fopen($file_path, 'r') or die("<p class=\"errorText\">Unable to connect to the server!</p>");
$file_txt = fread($file, filesize($file_path));
fclose($file);

$file_details = json_decode($file_txt, 1);

if (!is_array([$file_details])) {
    echo "<p class=\"errorText\">There is something wrong with this product. Please delete it or try again.</p>";
    die;
}

if (!isset($file_details['details']) || !isset($file_details['items']) || !isset($file_details['print_files']) || !isset($file_details['mockups'])) {
    echo "<p class=\"errorText\">There is something wrong with this product. Please delete it or try again.</p>";
    die;
}


if (!isset($file_details['details']['name']) || !isset($file_details['details']['price']) || !isset($file_details['details']['collection_id']) || !isset($file_details['details']['club_id']) || !isset($file_details['details']['league_id']) || !isset($file_details['details']['product_id']) || !isset($file_details['details']['discount'])) {
    echo "<p class=\"errorText\">Something is wrong with this product. Please remove it and re add it.</p>";
    die;
}

if(empty($file_details['details']["league_id"]) || empty($file_details['details']["club_id"]) || empty($file_details['details']["product_id"])) {
    echo "<p class=\"errorText\">Something is wrong with this product. Please remove it and re add it.</p>";
    die;
}

$print = $file_details['print_files'];
$mockups = $file_details['mockups'];
$items = $file_details['items'];

if (!is_array([$print]) || !is_array($mockups) || !is_array($items)) {
    echo "<p class=\"errorText\">There is something wrong with this product. Please delete it or try again.</p>";
    die;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../add/style.css">
</head>
<body>
    <!-- <h1 class="title">Admin Dashboard</h1> -->
    <form action="./" method="POST" autocomplete="off">
        <div class="input">
            <label for="product_name">Product name</label>
            <input type="text" id="product_name" value="<?php echo $row['name'];?>">
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="product_price">Product Price</label>
            <input type="text" id="product_price" value="<?php echo $row['price'];?>">
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="discount">Discount</label>
            <input type="text" id="discount" value="<?php echo $file_details['details']['discount'];?>">
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="league">League</label>
            <div class="select_parent">
                    <?php
                    $sql = "SELECT `name`, `id` FROM `leagues` WHERE 1";
                    $result = mysqli_query($conn, $sql);

                    $result_check = mysqli_num_rows($result);

                    if ($result_check === 0) {
                        ?>
                <select id="league">
                    <option value="" disabled selected>Not chosen</option>
                </select>
                <div class="custom_select invalid">Not Chosen</div>
            </div>
            <p class="error" style="display:block">Error finding leagues. Please create a league first at: <a href="#">admin/leagues</a></p>
        </div>                            
                        
                        <?php
                    }else {

                        ?>
                <select id="league">
                    <option value="" selected disabled>Not Chosen</option>
                    <?php
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($row['id'] == $file_details['details']['league_id']) {
                                $GLOBALS['select_value'] = $row['name'];
                                ?>
                                <option value="<?php echo $row['name'];?>" selected><?php echo $row['name'];?></option>
                                <?php
                            }else {
                            ?>
                            <option value="<?php echo $row['name'];?>"><?php echo $row['name'];?></option>
                            <?php
                            }
                        }
                    ?>
                </select>
                <div class="custom_select"><?php echo $GLOBALS['select_value'] ?? 'Not Chosen'; ?></div>
            </div>
            <p class="error"></p>
        </div>
                    <?php  
                    }
                    ?>

        <div class="input club_input">
            <label for="club">Club</label>
            <div class="select_parent">
                <select id="club">
                    <?php
                    
                    $sql = "SELECT `name`, `id` FROM `clubs` WHERE `league_id` = '" .$file_details['details']['league_id']."';";
                    $result = mysqli_query($conn, $sql);
                    $result_check = mysqli_num_rows($result);

                    if ($result_check === 0) {
                        //Product doesn't have a club
                        $GLOBALS['club_error'] = 'The product doesn\'t have a club. Please choose a club.';
                    }else {
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($row['id'] == $file_details['details']['club_id']) {
                                $GLOBALS['selected_club'] = $row['name'];
                                ?>
                        <option selected><?php echo $row['name']; ?></option>
                                <?php
                            }else {
                                ?>
                        <option><?php echo $row['name']; ?></option>
                                <?php
                            }
                        }
                    }
                    ?>
                </select>
                <div class="custom_select"><?php echo $GLOBALS['selected_club'] ?? "Not Chosen"; ?></div>
            </div>
            <p class="error"> <?php echo $GLOBALS['club_error'] ?? ''; ?></p>
        </div>

        <div class="input">
            <label for="collection">Collection</label>
            <div class="select_parent">
                <select id="collection">
                    <?php
                        if (is_numeric($file_details)) {
                            $sql = "SELECT `name`, `id` FROM `collections` WHERE 1;";

                            $result = mysqli_query($conn, $sql);
                            $result_check = mysqli_num_rows($result);

                            if ($result_check == 0) {
                                $GLOBALS['collection_error'] = 'The collection of this product is not found. You have to change it, or remove it';
                            }else {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    if ($row['id'] == $file_details['details']['collection_id']) {
                                        ?>
                        <option selected><?php echo $row['name']; ?></option>
                                        <?php
                                    }else {
    
                                        ?>
                        <option><?php echo $row['name']; ?></option>
                                        <?php
                                    }
                                }
                            }
                        }else {
                            ?>
                    <option selected value>Not Chosen</option>
                            <?php
                        }
                    ?>
                </select>
                <div class="custom_select">Not Chosen</div>
            </div>
            <p class="error"> <?php echo $GLOBALS['collection_error'] ?? '';?></p>
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
        <div class="phones" style="display:block">
            <h3 class="phones_title">Added Phone Models:</h3>
            <?php

                foreach ($items as $item) {
                    if (!isset($item['device_name']) || !isset($item['device_id'])) {
                        continue;
                    }

                    ?>
                    
                    <div class="btn">
                        <span class="device_name"> <?php echo $item['device_name']; ?></span>
                        <span class="device_id"><?php echo $item['device_id']; ?></span>
                        <button type="button" class="deleteBtn"></button>
                    </div>
                    
                    <?php
                }
            ?>
        </div>
        <div class="variants" style="display:flex;">
            <h3 class="variants_title">Added Variants:</h3>
            <?php

                foreach ($print as $p) {
                    if (!isset($p['img']) || !isset($p['name'])) {
                        continue;
                    }

                    ?>
                    <div class="card">
                        <img src="/phone/assets/print/<?php echo $p['img']; ?>" alt="print file">
                        <p class="variant_name"><?php echo $p['name']; ?></p>
                        <button type="button" class="deleteBtn">Delete</button>
                    </div>
                    <?php
                }
            ?>
        </div>
        <div class="mockups_section" style="display:flex">
            <h3 class="mockups_section_title">Added Mockups:</h3>
            
            <?php

                foreach ($mockups as $m) {
                    ?>
                    <div class="card">
                        <img src="/phone/assets/mockups/350/<?php echo $m; ?>" alt="Mockup file">
                        <p class="name">Mockup</p>
                        <button type="button" class="deleteBtn">Delete</button>
                    </div>
                    <?php
                }
            ?>

        </div>
        <ul class="errorAll"></ul>
        <ul class="successAll"></ul>
        <button class="addProduct">Update</button>
        <button type="button" class="delete" id="delete" data-id="<?php echo $id; ?>">  Delete Product</button>
    </form>
</body>
<script type="text/javascript">

$(document).ready(() => {
    let delete_product = document.getElementById('delete')
    delete_product.addEventListener('click', e => {
        if (delete_product.dataset?.id) {


            document.querySelector(".loading").style.display = 'block'
            while(document.querySelector('.errorAll').firstElementChild) {
                document.querySelector('.errorAll').firstElementChild.remove()
            }
            while(document.querySelector('.successAll').firstElementChild) {
                document.querySelector('.successAll').firstElementChild.remove()
            }
            document.querySelector('.successAll').textContent = ''
            document.querySelectorAll('.invalid').forEach(x => {
                x.classList.remove('invalid')
            })
            document.querySelectorAll('.error').forEach(x => {
                x.textContent = ''
            })
            document.querySelectorAll('.msg').forEach(x => {
                x.textContent = ''
            })
            document.querySelectorAll('.card').forEach(x => {
                x.style.boxShadow = '0px 0px 0px 2px #ccc'
            })

            $.ajax({
                url: './delete.php',
                method: 'POST',
                data: {
                    id: delete_product.dataset.id
                },
                complete: () => {
                    document.querySelector(".loading").style.display = 'none'
                },
                success: (data) => {
                    let script = document.createElement('script')
                    script.classList.add('returned_script')
                    script.innerHTML = data.trim()
                    let all = document.querySelectorAll('.returned_script')
                    for (let i = 0; i < all.length; i++) {
                        all[i].remove()
                    }
                    document.body.appendChild(script)

                },
                error: data => {
                    document.querySelector(".errorAll").textContent = "Error, something went wrong!"
                }
            })
        }
    })

//cancelling all label tags

let labels = Array.from($('label'))

labels.forEach(label => {
    label.addEventListener('click', e => {
        e.preventDefault() 
    })
})

//Adding the custom select

const all_selects = document.querySelectorAll('.select_parent select')

all_selects.forEach(select => {
    select.addEventListener('change', e => {
        e.target.nextElementSibling.textContent = e.target.options[e.target.selectedIndex].textContent
        if (e.target.nextElementSibling.classList.contains('invalid')) {
            e.target.nextElementSibling.classList.remove('invalid')
            e.target.parentElement.parentElement.querySelector('.error').textContent = ''
            e.target.parentElement.parentElement.querySelector('.error').style.display = 'none'
        }
    })
})

//Delete img preview
let preview_deleteBtn = [
    document.querySelector('.preview .deleteBtn'),
    document.querySelector('.preview_mockup .deleteBtn')
]
preview_deleteBtn.forEach(x => {
    x.addEventListener('click', e => {
        x.nextElementSibling.src = '//'
        x.parentElement.style.display = 'none'
        x.parentElement.previousElementSibling.querySelector('input[type=file]').value = ''
    })
})

const addPhone = document.querySelector('.addPhone')
addPhone.style.userSelect = 'none'
let icon = document.querySelectorAll('.addBtn .icon')

//Plus icon
for (let i = 0; i < icon.length; i++) {
    icon[i].parentElement.onclick = () => {
        if (!icon[i].parentElement.dataset.open) {
            return
        }
        let parent = document.querySelector(`.${icon[i].parentElement.dataset.open}`)
        
        if (parent.style.display == '' || parent.style.display == 'none') {
            parent.style.display = 'flex'
            icon[i].children[0].classList.remove('animateForward')
            icon[i].children[0].classList.add('animateBack')
        }else {
            parent.style.display = 'none'
            icon[i].children[0].classList.remove('animateBack')
            icon[i].children[0].classList.add('animateForward')
        }
    }
}


const file_input = document.querySelectorAll('input[type=file]')

let file_history = new Array


for (let i = 0; i < file_input.length; i++) {
    file_history.push('')
    file_input[i].addEventListener('change', e => {
        if (file_input[i].value.length === 0) {
            if (file_history[i].length > 0) {
                file_input[i].files = file_history[i]
            }
        }

        function preview_hide() {
            file_input[i].parentElement.parentElement.nextElementSibling.style.display= 'none'
            file_input[i].parentElement.parentElement.nextElementSibling.querySelector('img').src = '//'
        }

        //Cancelling for a better UX
        file_input[i].parentElement.nextElementSibling.style.display = 'none'
        file_input[i].parentElement.nextElementSibling.textContent = ''
        file_input[i].nextElementSibling.classList.remove('invalid')


        const [file] = file_input[i].files
        if (file) {
            if (file.type.indexOf('image/') < 0) {
                preview_hide()
                file_input[i].value = ''
                window.setTimeout(() => {
                    file_input[i].parentElement.nextElementSibling.textContent = 'Invalid file type. Please upload an image!'
                    file_input[i].parentElement.nextElementSibling.style.display = 'block'
                    file_input[i].nextElementSibling.classList.add('invalid')
                }, 100)
                return
            }
            const img = file_input[i].parentElement.parentElement.nextElementSibling.querySelector('img')
            img.src = URL.createObjectURL(file)
            const title = file_input[i].parentElement.parentElement.nextElementSibling.querySelector('.image_title')
            title.textContent = file.name            
            const preview = file_input[i].parentElement.parentElement.nextElementSibling
            preview.style.display = 'flex'
        }else {
            preview_hide()
            return
        }
        file_history[i] = file_input[i].files


    })
}

    
const addVariant = document.querySelector('.addVariant[type=button]')

const player_name = document.querySelector('#player_name')


addVariant.addEventListener('click', e => {
    //Cancelling for a better UX

    player_name.classList.remove('invalid')
    document.querySelector('.upload .custom_input_file').classList.remove('invalid')
    player_name.parentElement.querySelector('.error').style.display = 'none'
    document.querySelector('.upload .error').style.display = 'none'
    player_name.parentElement.querySelector('.error').textContent = ''
    document.querySelector('.upload .error').textContent = ''

    function hide_preview() {
        document.querySelector('.preview').style.display = 'none'
        document.querySelector('.preview img').src = '//'
    }

    let errorCheck = false
    if (player_name.value.trim().length === 0) {
        errorCheck = true
        window.setTimeout(() => {
            player_name.classList.add('invalid')
            player_name.parentElement.querySelector('.error').textContent = 'Please fill in this input'
            player_name.parentElement.querySelector('.error').style.display = 'block'
        }, 100)
    }
    const [file] = file_input[0].files
    if (!file) {
        errorCheck = true
        hide_preview()
        window.setTimeout(() => {
            document.querySelector('.upload .error').textContent = 'Please upload a file.'
            document.querySelector('.upload .error').style.display = 'block'
            document.querySelector('.upload .custom_input_file').classList.add('invalid')
        }, 100)
        return  
    }
    if (file.type.indexOf('image/') < 0){
        errorCheck = true
        hide_preview()
        window.setTimeout(() => {
            document.querySelector('.upload .error').textContent = 'The file type you uploaded is not allowed. Please upload an image'
            document.querySelector('.upload .error').style.display = 'block'
            document.querySelector('.upload .custom_input_file').classList.add('invalid')
        }, 100)
    }
    let existing_variants = document.querySelectorAll('.variants .card')

    existing_variants.forEach(variant => {
        if (variant.children[1].textContent.toLowerCase() == player_name.value.trim().toLowerCase()) {
            errorCheck = true
            window.setTimeout(() => {
                player_name.classList.add('invalid')
                player_name.parentElement.querySelector('.error').textContent = 'A variant with the same name already exist. Please change the name, or delete/edit the existing one.'
                player_name.parentElement.querySelector('.error').style.display = 'block'
            }, 100)
            return
        }
    })

    if (errorCheck) {
        return
    }
    const card = document.createElement('div')
    card.classList.add('card')

    const card_img = document.createElement('img')
    card_img.alt = 'print_file'
    card_img.src = URL.createObjectURL(file)

    const variant_name = document.createElement('p')
    variant_name.classList.add('variant_name')
    variant_name.appendChild(document.createTextNode(player_name.value.trim()))

    const deleteBtn = document.createElement('button')
    deleteBtn.classList.add('deleteBtn')
    deleteBtn.type = 'button'
    deleteBtn.appendChild(document.createTextNode('Delete'))
    deleteBtn.addEventListener('click', e => delete_(e))
    card.appendChild(card_img)
    card.appendChild(variant_name)
    card.appendChild(deleteBtn)
    document.querySelector('.variants').appendChild(card)
    document.querySelector('.variants').style.display = 'flex'
    
    //reset
    player_name.value = ''
    document.querySelector('.preview img').src = '//'
    document.querySelector('.preview').style.display = 'none'
    file_input[0].value = ''

})


const addMockup = document.querySelector('.addMockup_btn')
addMockup.addEventListener('click', e => {
    const [file] = file_input[1].files

    //Cancelling
    document.querySelector('.mockups .upload_mockup .custom_input_file').classList.remove('invalid')
    document.querySelector('.mockups .upload_mockup .error').style.display = 'none'
    document.querySelector('.mockups .upload_mockup .error').textContent = ''

    if (!file) {
        errorCheck = true
        window.setTimeout(() => {
            document.querySelector('.mockups .upload_mockup .error').textContent = 'Please upload a file'
            document.querySelector('.mockups .upload_mockup .error').style.display = 'block'
            document.querySelector('.mockups .upload_mockup .custom_input_file').classList.add('invalid')
        }, 100)
        return  
    }
    if (file.type.indexOf('image/') < 0){
        errorCheck = true
        window.setTimeout(() => {
            document.querySelector('.mockups .upload_mockup .error').textContent = 'The file type you uploaded is not allowed. Please upload an image'
            document.querySelector('.mockups .upload_mockup .error').style.display = 'block'
            document.querySelector('.mockups .upload_mockup .custom_input_file').classList.add('invalid')
        }, 100)
        return
    }

    const card = document.createElement('div')
    card.classList.add('card')

    const card_img = document.createElement('img')
    card_img.alt = 'Mockup File'
    card_img.src = URL.createObjectURL(file)

    const name = document.createElement('p')
    name.classList.add('name')
    name.appendChild(document.createTextNode('Mockup'))

    const deleteBtn = document.createElement('button')
    deleteBtn.classList.add('deleteBtn')
    deleteBtn.type = 'button'
    deleteBtn.appendChild(document.createTextNode('Delete'))
    deleteBtn.addEventListener('click', e => delete_(e))
    card.appendChild(card_img)
    card.appendChild(name)
    card.appendChild(deleteBtn)
    document.querySelector('.mockups_section').appendChild(card)
    document.querySelector('.mockups_section').style.display = 'flex'
    
    //reset
    document.querySelector('.preview_mockup img').src = '//'
    document.querySelector('.preview_mockup').style.display = 'none'
    file_input[1].value = ''

})



const addPhone_btn = document.querySelector('.addPhone[type=button]')

addPhone_btn.addEventListener('click', e => {
    const phone_model_input = document.querySelector('.phone_list .input select')
    const device_id = document.querySelector('.phone_list .input input')

    //Cancelling the error messages for a better UX

    phone_model_input.parentElement.parentElement.querySelector('.error').style.display = 'none'
    phone_model_input.parentElement.parentElement.querySelector('.error').textContent = ''
    phone_model_input.nextElementSibling.classList.remove('invalid')

    device_id.classList.remove('invalid')
    device_id.parentElement.querySelector('.error').style.display = 'none'
    device_id.parentElement.querySelector('.error').textContent = ''



    let errorCheck = false

    if (phone_model_input.selectedIndex === 0) {
        
        errorCheck = true
        window.setTimeout(() => {
            document.querySelector('.phone_list .input .custom_select').classList.add('invalid')
            document.querySelectorAll('.phone_list .input .error')[0].textContent = 'Please choose a phone model.'

            document.querySelectorAll('.phone_list .input .error')[0].style.display = 'block'
        }, 100)
    }

    if (device_id.value.trim().length === 0) {

        errorCheck = true
        window.setTimeout(() => {
            device_id.classList.add('invalid')
            device_id.parentElement.querySelector('.error').textContent = 'Please fill in this input.'
            device_id.parentElement.querySelector('.error').style.display = 'block'
        }, 100)
    }

    if (errorCheck) {
        return
    }
    
    //check if phone is already added
    let phone_elt = document.querySelectorAll('.phones .btn')
    if (phone_elt.length > 0) {
        let check = false;
        for (let i = 0; i < phone_elt.length; i++) {
            if (phone_elt[i].children[0].textContent.toLowerCase() == phone_model_input.children[phone_model_input.selectedIndex].textContent.toLowerCase()){
                //Phone model exist
                check = true
                window.setTimeout(() => {
                    phone_model_input.nextElementSibling.classList.add('invalid')
                    phone_model_input.parentElement.parentElement.querySelector('.error').textContent = 'You have already added This phone model. Please delete the existing one first.'
                    phone_model_input.parentElement.parentElement.querySelector('.error').style.display = 'block'
                }, 100)
            }
            if (phone_elt[i].children[1].textContent.toLowerCase() == device_id.value.trim().toLowerCase()) {
                check = true
                window.setTimeout(() => {
                    device_id.classList.add('invalid')
                    device_id.parentElement.querySelector('.error').textContent = 'A phone model with this ID already exist. Please delete it first.'
                    device_id.parentElement.querySelector('.error').style.display = 'block'
                }, 100)
            }
            if (check === true) {
                return
            }
        }
    }


    const btn = document.createElement('div')
    btn.classList.add('btn')

    const device_name = document.createElement('span')
    device_name.classList.add('device_name')
    device_name.appendChild(document.createTextNode(phone_model_input.children[phone_model_input.selectedIndex].textContent))
   
    const device_id_elt = document.createElement('span')
    device_id_elt.classList.add('device_id')
    device_id_elt.appendChild(document.createTextNode(device_id.value.trim()))

    const delete_btn = document.createElement('button')
    delete_btn.classList.add('deleteBtn')
    delete_btn.type = 'button'
    delete_btn.addEventListener('click', e => delete_(e))

    btn.appendChild(device_name)
    btn.appendChild(device_id_elt)
    btn.appendChild(delete_btn)
    document.querySelector('.phones').appendChild(btn)
    document.querySelector('.phones').style.display = 'block'
    phone_model_input.selectedIndex = 0
    phone_model_input.nextElementSibling.textContent = phone_model_input.options[0].textContent
    device_id.value = ''

})

const allInputs = document.querySelectorAll('input')
allInputs.forEach(input => {
    if (input.type == 'file') {
        return
    }
    input.addEventListener('keyup', e => {
        if (e.constructor.name == 'KeyboardEvent') {
            return
        }
        if (e.target.value.trim().length > 0) {
            e.target.classList.remove('invalid')
            e.target.parentElement.querySelector('.error').textContent = ''
            e.target.parentElement.querySelector('.error').style.display = 'none'
        }
    })
})



$('form').submit(e => {
    e.preventDefault()

    document.querySelector(".loading").style.display = 'block'
    while(document.querySelector('.errorAll').firstElementChild) {
        document.querySelector('.errorAll').firstElementChild.remove()
    }
    while(document.querySelector('.successAll').firstElementChild) {
        document.querySelector('.successAll').firstElementChild.remove()
    }
    document.querySelector('.successAll').textContent = ''
    document.querySelectorAll('.invalid').forEach(x => {
        x.classList.remove('invalid')
    })
    document.querySelectorAll('.error').forEach(x => {
        x.textContent = ''
    })
    document.querySelectorAll('.msg').forEach(x => {
        x.textContent = ''
    })
    document.querySelectorAll('.card').forEach(x => {
        x.style.boxShadow = '0px 0px 0px 2px #ccc'
    })

    let added_devices = new Array
    let added_variants = new Array
    let added_mockups = new Array
    let mockups_92 = new Array

    for (let i = 0; i < document.querySelectorAll('.phones .btn').length; i++) {
        added_devices.push({
            device_name: document.querySelectorAll('.phones .btn')[i].querySelector('.device_name').textContent, device_id: document.querySelectorAll('.phones .btn')[i].querySelector('.device_id').textContent
        })
    }

    for (let i = 0; i < document.querySelectorAll('.variants .card').length; i++) {
        added_variants.push({
            img: (function (){
                let img = new Image()
                img.src = document.querySelectorAll('.variants .card')[i].querySelector('img').src

                let width = img.width
                let height = img.height
                let canvas = document.createElement('canvas')
                canvas.width  = width
                canvas.height = height
                let ctx = canvas.getContext("2d");  // Get the "context" of the canvas
                ctx.drawImage(img,0,0,width,height);  // Draw your image to the canvas
                
                return jpegFile = canvas.toDataURL("image/png")



            }()),
            name: document.querySelectorAll('.variants .card')[i].querySelector('.variant_name').textContent
        })
    }
    for (let i = 0; i < document.querySelectorAll('.mockups_section .card').length; i++) {
        added_mockups.push(
            (function (){
                let img = new Image()
                img.src = document.querySelectorAll('.mockups_section .card')[i].querySelector('img').src

                let width = img.width
                let height = img.height
                let canvas = document.createElement('canvas')
                canvas.width  = width
                canvas.height = height
                let ctx = canvas.getContext("2d");  // Get the "context" of the canvas
                ctx.drawImage(img,0,0,width,height);  // Draw your image to the canvas
                
                return jpegFile = canvas.toDataURL("image/png")
            }())
        )
        mockups_92.push(
            (function (){
                let img = new Image()
                img.src = document.querySelectorAll('.mockups_section .card')[i].querySelector('img').src

                let width = 92
                let height = 92
                let canvas = document.createElement('canvas')
                canvas.width  = width
                canvas.height = height
                let ctx = canvas.getContext("2d");  // Get the "context" of the canvas
                ctx.drawImage(img,0,0,width,height);  // Draw your image to the canvas
                
                return jpegFile = canvas.toDataURL("image/png")
            }())
        )
    }


    $.ajax({
        url: './update.php',
        method: "POST",
        data: {
            product_name: document.getElementById('product_name').value,
            product_price: document.getElementById('product_price').value,
            discount: document.getElementById('discount').value,
            league: document.getElementById('league').options[document.getElementById('league').selectedIndex].value || '',
            club: document.getElementById('club').options[document.getElementById('club').selectedIndex].value || '',
            collection: document.getElementById('collection').options[document.getElementById('collection').selectedIndex].value || '',
            phones: JSON.stringify(added_devices),
            variants: JSON.stringify(added_variants),
            mockups: JSON.stringify(added_mockups),
            mockups_92: JSON.stringify(mockups_92),
            mockup_156: JSON.stringify(added_mockups[0]) || "",
            id: currentId
        },
        complete: () => {
            document.querySelector(".loading").style.display = 'none'
        },
        success: (data) => {
            let script = document.createElement('script')
            script.classList.add('returned_script')
            script.innerHTML = data.trim()
            let all = document.querySelectorAll('.returned_script')
            for (let i = 0; i < all.length; i++) {
                all[i].remove()
            }
            document.body.appendChild(script)

        },
        error: data => {
            document.querySelector(".errorAll").textContent = "Error, something went wrong!"
        }
    })
})

const league_select = document.querySelector('#league')

league_select.addEventListener('change', e => {
    if (league_select.selectedIndex > 0) {
        document.querySelector('.loading').style.display = 'block'
        while (document.querySelector("#club").firstElementChild) {
            document.querySelector("#club").firstElementChild.remove()
        }
        let option = document.createElement('option')
        option.textContent = 'Not Chosen'
        option.value = '0'
        option.selected = true
        option.disabled = true
        document.querySelector("#club").appendChild(option)
        document.querySelector("#club").parentElement.parentElement.style.display = 'none'
        document.querySelector("#club").nextElementSibling.textContent = 'Not Chosen'
        $.ajax({
            url: '../add/leagueCheck.php',
            method: "POST",
            data: {
                league: league_select.options[league_select.selectedIndex].value.trim()
            },
            complete: () => {
                document.querySelector('.loading').style.display = 'none'
            },
            success: data => {
                let script = document.createElement('script')
                script.classList.add('returned_script')
                script.innerHTML = data.trim()
                let all = document.querySelectorAll('.returned_script')
                for (let i = 0; i < all.length; i++) {
                    all[i].remove()
                }
                document.body.appendChild(script)
            },
            error: data => {
                document.querySelector("#league").nextElementSibling.classList.add("invalid")
                document.querySelector("#league").parentElement.nextElementSibling.textContent = "Error, something went wrong!"
            }
        })
    }
})


function delete_(e) {
    const parent = e.target.parentElement

    let length =  (parent.parentElement.querySelectorAll('.deleteBtn').length) - 1
    if (length === 0) {
        parent.parentElement.style.display = 'none'
    }
    parent.remove()
}



delete_btn_in_document = document.querySelectorAll('.deleteBtn');

for (let i = 0; i < delete_btn_in_document.length; i++) {
    delete_btn_in_document[i].addEventListener('click', e => delete_(e))
}

})
</script>
</html>