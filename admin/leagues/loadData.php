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
    if (strpos($_SERVER['HTTP_REFERER'], '/admin/leagues') <= 0){
        http_response_code(404);
        die;
    }
}


if (!isset($_POST['id'])) {
    http_response_code(404);
    die;
}

require_once "../../conn/conn.php";
$conn = $GLOBALS['conn'];


$id = mysqli_real_escape_string($conn, trim($_POST['id']));


if (empty($id)) {
    echo "<p class='msg'>Error loading the chosen league. Please try a different league</p>";
}


if (!is_numeric($id) || (strlen($id) > 11 || $id < 0)) {
    echo "<p class='msg'>Error loading the chosen league. Please try a different league</p>";
    die;
}

//Check if league exist

$sql = "SELECT * FROM `leagues` WHERE `id` = ?  ";
$stmt = mysqli_stmt_init($conn);

mysqli_stmt_prepare($stmt, $sql) or die("<p class='msg'>Error, something went wrong! Please try again later.</p>");


mysqli_stmt_bind_param($stmt, 's', $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$result_check = mysqli_num_rows($result);

if ($result_check === 0) {
    //No result
    echo "<p class='msg'>Error loading the chosen league. Please try a different league</p>";
    die;
}

//League found

$row = mysqli_fetch_assoc($result) or die("<p class='msg'>Error, something went wrong! Please try again later.</p>");
?>

<form action="./updateLeague.php" method="POST" class="updateLeague_section" id="updateLeague" data-id="<?php echo $row['id']; ?>" autocomplete="off">
    <div class="input">
        <label for="update_name">Leauge Name</label>
        <input type="text" id="update_name" value="<?php echo $row['name']; ?>">
        <p class="error"></p>
    </div>
    <div class="upload">
        <label for="input_file_2">League Logo</label>
        <div class="custom_input_file_parent">
            <input type="file" id="input_file_2">
            <div class="custom_input_file">Choose File</div>
        </div>
        <p class="error"></p>
    </div>
    <div class="preview" style="display: flex">
        <button type="button" class="deleteBtn cross">×</button>
        <img src="../../assets/leagues/<?php echo $row['img'] . "?". mt_rand(10000, 99999); ?>" alt="Print file">
        <h3 class="image_title"></h3>
    </div>
    <ul class="update_errorAll"></ul>
    <ul class="update_successAll"></ul>
    <button type="submit">Update League</button>
    <button type="button" class="delete" id="delete" data-id="<?php echo $row['id']; ?>">Delete League</button>
    <button class="reload" type="button"></button>
</form>




<script type="text/javascript">
    document.querySelector('.result').style.display = 'none'
    
    $(document).ready(() => {
        let delete_btn = document.querySelector('.update .deleteBtn')
        let reload = document.querySelector('.reload')


        reload.addEventListener('click', e => {
            loadData(document.querySelector('.chosen_league'))
        })
        let label = document.querySelectorAll('label')
        label.forEach(l => {
            l.addEventListener('click', e => {
                e.preventDefault()
            })
        })

        delete_btn.addEventListener('click', e => {
            delete_btn.nextElementSibling.src = '//'
            delete_btn.parentElement.style.display = 'none'
            delete_btn.parentElement.previousElementSibling.querySelector('input[type=file]').value = ''
        })

        let file_element = document.querySelector('#input_file_2')
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

        $('#updateLeague').submit(e => {
            e.preventDefault()
            document.querySelector('.loading').style.display = 'block'
            let li = Array.from(document.querySelectorAll('.update_errorAll li'))
            li = [...li, ...Array.from(document.querySelectorAll('.update_successAll li'))]
            li.forEach(l => {
                l.remove()
            })

            let invalid = document.querySelectorAll('#updateLeague .invalid')

            invalid.forEach(i => {
                i.classList.remove('invalid')
            })

            let error = document.querySelectorAll('#updateLeague .error')
            error.forEach(e => {
                e.textContent = ''
            })

            $.ajax({
                url: './updateleague.php',
                method: "POST",
                data: {
                    name: document.getElementById('update_name').value?.trim(),
                    id: document.querySelector('#updateLeague').dataset?.id,
                    img: (function (){
                        let img = new Image()
                        img.src = document.querySelector('.update .preview img').src
        
                        let width = img.width
                        let height = img.height
                        let canvas = document.createElement('canvas')
                        canvas.width  = width
                        canvas.height = height
                        let ctx = canvas.getContext("2d");  // Get the "context" of the canvas
                        ctx.drawImage(img,0,0,width,height);  // Draw your image to the canvas
                        return jpegFile = canvas.toDataURL("image/png")
                }())
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
                error: err => document.querySelector(".update_errorAll").textContent = "Error, something went wrong!"
            })
        })
        let delete_league = document.getElementById('delete')
        delete_league.addEventListener('click', e => {
            let li = Array.from(document.querySelectorAll('.update_errorAll li'))
            li = [...li, ...Array.from(document.querySelectorAll('.update_successAll li'))]
            li.forEach(l => {
                l.remove()
            })

            let invalid = document.querySelectorAll('#updateLeague .invalid')

            invalid.forEach(i => {
                i.classList.remove('invalid')
            })

            let error = document.querySelectorAll('#updateLeague .error')
            error.forEach(e => {
                e.textContent = ''
            })
            if(typeof window.entered_password != 'undefined') {
                $.ajax({
                    url: './deleteleague.php',
                    method: 'POST',
                    data: {
                        id: delete_league.dataset?.id,
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
                    error: err => document.querySelector(".update_errorAll").textContent = "Error, something went wrong!"
                })
            }else {
                document.querySelector('.pwdPopup-parent').style.display = 'flex'
            }
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
                url: './deleteleague.php',
                method: 'POST',
                data: {
                    id: delete_league.dataset?.id,
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
                error: err => document.querySelector(".update_errorAll").textContent = "Error, something went wrong!"
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