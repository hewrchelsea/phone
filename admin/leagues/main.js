$(document).ready(() => {

    let label = document.querySelectorAll('label')
    label.forEach(l => {
        l.addEventListener('click', e => {
            e.preventDefault()
        })
    });


    let icon = document.querySelectorAll('.open .icon')
    
    
    //Plus icon
    for (let i = 0; i < icon.length; i++) {
        icon[i].parentElement.onclick = () => {
            if (!icon[i].parentElement.dataset.open) {
                return
            }
            let parent = document.querySelector(`.${icon[i].parentElement.dataset.open}`)
            
            if (parent.style.display == '' || parent.style.display == 'none') {
                parent.style.display = 'block'
                icon[i].children[0].classList.remove('animateForward')
                icon[i].children[0].classList.add('animateBack')
            }else {
                parent.style.display = 'none'
                icon[i].children[0].classList.remove('animateBack')
                icon[i].children[0].classList.add('animateForward')
            }
        }
    }


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
        file_element.parentElement.nextElementSibling.textContent = ''
        file_element.nextElementSibling.classList.remove('invalid')


        const [file] = file_element.files
        if (file) {
            if (file.type.indexOf('image/') < 0) {
                preview_hide()
                file_element.value = ''
                window.setTimeout(() => {
                    file_element.parentElement.nextElementSibling.textContent = 'Invalid file type. Please upload an image!'
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



    let addLeagueBtn = document.querySelector("#addLeague")


    $('#addLeague').submit(e => {
        e.preventDefault()
        document.querySelector('.loading').style.display = 'block'
        let all = document.querySelectorAll("#addLeague .invalid")
        all.forEach(x => x.classList.remove('invalid'))
        let error = Array.from(document.querySelectorAll('#addLeague .error'))
        error.push(document.querySelector('#addLeague .errorAll'), document.querySelector('#addLeague .successAll'))
        error.forEach(e => {
            e.textContent = ''
        });
        $.ajax({
            data: {
                name: document.querySelector('#name').value?.trim(),
                img: (function (){
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
            },
            complete: () => document.querySelector('.loading').style.display = 'none',
            url: "./addleague.php",
            method: "POST",
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
            error: err => document.querySelector("#addLeague .errorAll").textContent = "Error, something went wrong!"
        })
    })
    let searchBar = document.getElementById('searchBar')

    let check = 'clicked'
    $('#search_league').submit(e => {
        e.preventDefault()

        $('.result').load('./searchleague.php', {
            name: searchBar.value.trim(),
            check
        })
        check = 'clicked'
    })


    searchBar.addEventListener('keyup', e => {
        check = 'notClicked'
        $('#search_league').submit()
    })

})
function loadData(e) {
    let updateLeague = document.querySelector('#updateLeague')
    if (updateLeague && updateLeague.dataset && 'id' in updateLeague.dataset) {
        $('.update').load('./loadData.php', {
            id: updateLeague.dataset.id.trim()
        })
    }else {
        if (e && e.dataset && 'id' in e.dataset) {
            $('.update').load('./loadData.php', {
                id: e.dataset.id.trim()
            })
        }
    }
}