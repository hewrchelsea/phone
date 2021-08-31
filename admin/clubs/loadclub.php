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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/clubs/update') <= 0){
        http_response_code(404);
        die;
    }
}


if (!isset($_POST['id']) ) {
    http_response_code(404);
    die;
}

require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];

$id = mysqli_real_escape_string($conn, trim($_POST['id']));


if (!is_numeric($id)) {
    echo "<p class=\"error\">Error Loading this Club. Please try again later.</p>";
    die;
}

if ($id < 0 || strlen($id) > 11) {
    echo "<p class=\"error\">Error Loading this Club. Please try again later.</p>";
    die;
}

//Check if the club exist

$sql = "SELECT * from `clubs` WHERE `id` = ?";

$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die("<p class=\"error\">Error Loading this Club. Please try again later.</p>");

mysqli_stmt_bind_param($stmt, 'i', $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    //Product not found!¨
    echo "<p class=\"error\">Product not found please search again.</p>";
}

if ($row = mysqli_fetch_assoc($result)) {
    ?>
        <form action="./add.php" method="POST" id="update_form" autocomplete="off">
        <div class="input">
            <label for="club_name">Club name</label>
            <input type="text" id="club_name" value="<?php echo $row['name']; ?>">
            <p class="error"></p>
        </div>
        <div class="input">
            <label for="league">League</label>
            <div class="select_parent">
                <select id="league">
                    <option value="0" selected disabled>Not Chosen</option>
                    <?php
                    
                    require_once "../../conn/conn.php";
                    $conn = $GLOBALS['conn'];

                    $sql = "SELECT * FROM `leagues` WHERE 1";
                    $result = mysqli_query($conn, $sql);
                    $result_check = mysqli_num_rows($result); 
                    
                    if ($result_check !== 0) {
                        while ($row_2 = mysqli_fetch_assoc($result)) {
                            if ($row_2['id'] == $row['league_id']) {
                                $GLOBALS['chosen_league'] = $row_2['name'];
                                ?>
                            <option selected><?php echo $row_2['name']; ?></option>      
                                <?php
                            }else {
                            ?>
                            
                            <option><?php echo $row_2['name']; ?></option>
                            
                            <?php
                            }
                        }
                    }

                    ?>
                </select>
                <div class="custom_select"><?php echo $GLOBALS['chosen_league'] ?? 'Not Chosen'; ?></div>
            </div>
            <p class="error"></p>
        </div>
        <div class="upload">
            <label for="input_file">Club Logo</label>
            <div class="custom_input_file_parent">
                <input type="file" id="input_file">
                <div class="custom_input_file">Choose File</div>
            </div>
            <p class="error"></p>
        </div>
        <div class="preview" style="">
            <button type="button" class="deleteBtn cross">×</button>
            <img src="/phone/assets/clubs/<?php echo $row['img'] . "?". mt_rand(10000, 99999); ?>" alt="Print file">
            <h3 class="image_title"></h3>
        </div>
        <p class="errorAll"></p>
        <p class="successAll"></p>
        <button class="updateClub">Update Club</button>
        <button class="delete delete_club" type="button"  data-id="<?php echo $row['id']; ?>">Delete Club</button>
        <button class="reload" type="button" onclick="loadData(this)" data-id="<?php echo $row['id']; ?>"></button>
    </form>
    <script type="text/javascript">

        $(document).ready(() => {

            let all_select = document.querySelectorAll('select')

            all_select.forEach(s => {
                s.addEventListener('change', e => {
                    s.nextElementSibling.textContent = s.options[s.selectedIndex].textContent
                })
            })
            
            let label = document.querySelectorAll('label')
            label.forEach(l => {
                l.addEventListener('click', e => {
                    e.preventDefault()
                })
            })

            let delete_club = document.querySelector('.delete_club')
            delete_club.addEventListener('click', e => {
                if (!'id' in e.target.dataset)  {
                    document.querySelector('.errorAll').textContent = 'Error Couldn\'t delete club!'
                    return
                }
                document.querySelector('.errorAll').textContent = ''
                document.querySelector('.successAll').textContent = ''
                if(typeof window.entered_password != 'undefined') {
                    $.ajax({
                        url: './deleteclub.php',
                        method: 'POST',
                        data: {
                            id: delete_club.dataset?.id,
                            pwd: window.entered_password
                        },
                        complete: () => {
                            document.querySelector('#pwd').value = '',
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
                        error: err => document.querySelector(".errorAll").textContent = "Error, something went wrong!"
                    })
                }else {
                    document.querySelector('.pwdPopup-parent').style.display = 'flex'
                }
            })
            
            let file_history = ''
    
            let file_element = document.querySelector('#input_file')
            file_element.addEventListener('change', e => {
                if (file_element.value.length === 0) {
                    if (file_history.length > 0) {
                        file_element.files = file_history
                    }
                }

                function preview_hide() {
                    file_element.parentElement.parentElement.nextElementSibling.style.display= 'none'
                    file_element.parentElement.parentElement.nextElementSibling.querySelector('img').src = '//'
                }

                //Cancelling for a better UX
                file_element.parentElement.nextElementSibling.style.display = 'none'
                file_element.parentElement.nextElementSibling.textContent = ''
                file_element.nextElementSibling.classList.remove('invalid')


                const [file] = file_element.files
                if (file) {
                    if (file.type.indexOf('image/') < 0) {
                        preview_hide()
                        file_element.value = ''
                        window.setTimeout(() => {
                            file_element.parentElement.nextElementSibling.textContent = 'Invalid file type. Please upload an image!'
                            file_element.parentElement.nextElementSibling.style.display = 'block'
                            file_element.nextElementSibling.classList.add('invalid')
                        }, 100)
                        return
                    }
                    const img = file_element.parentElement.parentElement.nextElementSibling.querySelector('img')
                    img.src = URL.createObjectURL(file)
                    const title = file_element.parentElement.parentElement.nextElementSibling.querySelector('.image_title')
                    title.textContent = file.name            
                    const preview = file_element.parentElement.parentElement.nextElementSibling
                    preview.style.display = 'flex'
                }else {
                    preview_hide()
                    return
                }
                file_history = file_element.files
            })

            let preview_deleteBtn = document.querySelector('.preview .deleteBtn')
            
            preview_deleteBtn.addEventListener('click', e => {
                preview_deleteBtn.nextElementSibling.src = '//'
                preview_deleteBtn.parentElement.style.display = 'none'
                preview_deleteBtn.parentElement.previousElementSibling.querySelector('input[type=file]').value = ''
            })


            let all_input = document.querySelectorAll('input[type=text]')
            for (let i = 0; i < all_input.length; i++) {
                all_input[i].addEventListener('keyup', e => {
                    all_input[i].classList.remove('invalid')
                    all_input[i].nextElementSibling.textContent = ''
                })
            }

            let select = document.querySelectorAll('select')
            for (let i = 0; i < select.length; i++) {
                select[i].addEventListener('change', e => {
                    select[i].nextElementSibling.classList.remove('invalid')
                    select[i].parentElement.nextElementSibling.textContent = ''
                })
            }
            $('#update_form').submit(e => {
                e.preventDefault()
                document.querySelector('.loading').style.display = 'block'

                document.querySelector('.errorAll').textConent = ''
                document.querySelector('.successAll').textConent = ''

                let name = document.getElementById('club_name').value.trim()
                let id = document.querySelector('.chosen_club').dataset.id
                let img = (function (){
                    let img = new Image()
                    img.src = document.querySelector('.preview img').src

                    let width = img.width
                    let height = img.height
                    let canvas = document.createElement('canvas')
                    canvas.width  = width
                    canvas.height = height
                    let ctx = canvas.getContext("2d");  // Get the "context" of the canvas
                    ctx.drawImage(img,0,0,width,height);  // Draw your image to the canvas
                    return jpegFile = canvas.toDataURL("image/png")
                }())
                let league = document.getElementById('league').options[document.getElementById('league').selectedIndex].value

                let array = Array.from(document.querySelectorAll('.error'))
                array.push(document.querySelector('.errorAll'), document.querySelector('.successAll'))
                array.forEach(x => x.textContent = '')
                document.querySelectorAll('.invalid').forEach(x => x.classList.remove('invalid'))
                $.ajax({
                    method: "POST",
                    url: "./updateclubs.php",
                    data: {
                        name,
                        id,
                        img,
                        league
                    },
                    complete: () => {
                        document.querySelector('.loading').style.display = 'none'
                    },
                    success: data => {
                        let script = document.createElement('script')
                        script.classList.add('returned_script')
                        let all = document.querySelectorAll('.returned_script')
                        for (let i = 0; i < all.length; i++) {
                            all[i].remove()
                        }

                        script.innerHTML = data
                        document.body.appendChild(script)
                    },
                    error: err => console.log('Error:', err)
                })
            })

            $('.pwdPopup').submit(e => {
                e.preventDefault()
                document.querySelector('.loading').style.display = 'block'
                document.querySelector('#pwd').classList.remove('invalid')
                document.querySelector('#pwd').nextElementSibling.textContent = ''
                if (document.querySelector('#pwd').value?.length === 0) {
                    document.querySelector('.loading').style.display = 'none'
                    document.querySelector('#pwd').classList.add('invalid')
                    document.querySelector('#pwd').nextElementSibling.textContent = 'Please fill in this input'
                    return
                }
                window.entered_password = document.querySelector('#pwd').value
                $.ajax({
                    url: './deleteclub.php',
                    method: 'POST',
                    data: {
                        id: delete_club.dataset?.id,
                        pwd: document.querySelector('#pwd').value || ''
                    },
                    complete: () => {
                        document.querySelector('#pwd').value = ''
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
                    error: err => document.querySelector(".errorAll").textContent = "Error, something went wrong!"
                })
            })
            document.querySelector('.pwdPopup-parent').addEventListener('click', e => {
                if (e.target.classList.contains('pwdPopup-parent')) {
                    e.target.querySelector('input').value = ''
                    e.target.style.display = 'none'
                }
            })
        })
    </script>
    <?php
}