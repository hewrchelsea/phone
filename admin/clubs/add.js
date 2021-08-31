$(document).ready(() => {

    let label = document.querySelectorAll('label')
    label.forEach(l => {
        l.addEventListener('click', e => {
            e.preventDefault()
        })
    })

    let all_select = document.querySelectorAll('select')

    all_select.forEach(s => {
        s.addEventListener('change', e => {
            s.nextElementSibling.textContent = s.options[s.selectedIndex].textContent
        })
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

    let addClub = document.querySelector('.addClub')

    addClub.addEventListener('click', e => {
        let check = false
        let club_name = document.getElementById('club_name')
        let league = document.getElementById('league')
        let input_file = document.getElementById('input_file')

        club_name.nextElementSibling.textContent = ''
        club_name.classList.remove('invalid')
        league.parentElement.nextElementSibling.textContent = ''
        league.nextElementSibling.classList.remove('invalid')
        input_file.parentElement.nextElementSibling.textContent = ''
        input_file.nextElementSibling.classList.remove('invalid')

        window.setTimeout(() => {
            if (club_name.value.trim().length === 0) {
                check = true
                club_name.nextElementSibling.textContent = 'Please fill in this input'
                club_name.classList.add('invalid')
            }
        
        
            if (league.selectedIndex === 0) {
                check = true
        
                league.parentElement.nextElementSibling.textContent = 'Please fill in this input'
        
                league.nextElementSibling.classList.add('invalid')
        
            }
            
        
            if (input_file.value.length === 0) {
                check = true
                input_file.parentElement.nextElementSibling.textContent = 'Please fill in this input'
                input_file.nextElementSibling.classList.add('invalid')
            }

            let cards = document.querySelectorAll('.added_clubs .card .club_name')

            if(cards.length >= 20) {
                console.log('There is too many clubs. You can add max 20 at once!')
                return
            }

            cards.forEach(c => {
                if (c.textContent.trim().toLowerCase() == club_name.value.trim().toLowerCase()) {
                    check = true
                    club_name.nextElementSibling.textContent = 'You have added a club with the same name.'
                    club_name.classList.add('invalid')
                }
            })
        
            if (check === true) {
                return
            }
        
        
            document.querySelector('.addAll').style.display = 'block'

            let added_clubs = document.querySelector('.added_clubs')
        
            let img = document.createElement('img')
            img.alt = 'Club'
            img.src = document.querySelector('.preview img').src
        
            let club_name_elt = document.createElement('p')
            club_name_elt.classList.add('club_name')
            club_name_elt.appendChild(document.createTextNode(club_name.value.trim()))

            let league_name_elt = document.createElement('p')
            league_name_elt.classList.add('league_name')
            league_name_elt.appendChild(document.createTextNode(league.options[league.selectedIndex].textContent.trim()))
        


            let deleteBtn = document.createElement('button')
            deleteBtn.classList.add('deleteBtn')
            deleteBtn.type = 'button'
            deleteBtn.appendChild(document.createTextNode('Delete'))
            deleteBtn.addEventListener('click', e => delete_(e))
        
            let card = document.createElement('div')
            card.classList.add('card')
        
            let msg = document.createElement('div')
            msg.classList.add('msg')



            card.appendChild(img)
            card.appendChild(club_name_elt)
            card.appendChild(league_name_elt)
            card.appendChild(msg)
            card.appendChild(deleteBtn)
        
            added_clubs.appendChild(card)

            added_clubs.style.display = 'flex'


            //RESET
            club_name.value = ''
            input_file.value = ''
            league.selectedIndex = 0
            league.nextElementSibling.textContent = 'Not Chosen'
            document.querySelector('.preview').style.display = 'none'
            document.querySelector('.preview img').src = '//'
        }, 100)
    })

    $('form').submit(e => {

        document.querySelector('.loading').style.display = 'block'

        let invalid = document.querySelectorAll('.invalid')
        invalid.forEach(i => i.classList.remove('invalid'))

        let error = Array.from(document.querySelectorAll('.error'))
        error = [...error, ...Array.from(document.querySelectorAll('.msg'))]
        error.forEach(e => e.textContent = '')

        let li = Array.from(document.querySelectorAll('.errorAll li'))
        li = [...li, ...Array.from(document.querySelectorAll('.successAll li'))]
        li.forEach(l => l.remove())

        let data = new Array()

        let card = document.querySelectorAll('.card')

        card.forEach(c => {
            c.style.boxShadow = '0px 0px 0px 2px #ccc'
        })


        if (card.length > 20) {
            let elt = document.createElement('li')
            elt.appendChild(document.createTextNode('There are too many clubs. You can not add more than 20 clubs!'))
            document.querySelector('.errorAll').appendChild(elt)
            document.querySelector('.loading').style.display = 'none'
            return
        }

        for (let i = 0; i < card.length; i++) {

            card[i].style.boxShadow = '0px 0px 0px 2px #ccc'

            card[i].querySelector('.msg').textContent = ''
            let obj = new Object()

            obj.img = (function (){
                let img = new Image()
                img.src = card[i].querySelector('img').src
                let width = img.width
                let height = img.height
                let canvas = document.createElement('canvas')
                canvas.width  = width
                canvas.height = height
                let ctx = canvas.getContext("2d");  // Get the "context" of the canvas
                ctx.drawImage(img,0,0,width,height);  // Draw your image to the canvas
                return jpegFile = canvas.toDataURL("image/png")
            }())

            obj.name = card[i].querySelector('.club_name').textContent

            obj.league = card[i].querySelector('.league_name').textContent
            data.push(obj)
        }
        e.preventDefault()
        $.ajax({
            url: "./addClubs.php",
            method: "POST",
            data: {
                data: JSON.stringify(data)
            },
            complete: () => {
                document.querySelector('.loading').style.display = 'none'
            },
            success: data => {

                if (document.querySelector('.result_script')) {
                    document.querySelector('.result_script').remove()
                }

                let script = document.createElement('script')
                script.classList.add('result_script')
                script.innerHTML = data
                document.body.appendChild(script)
            },
            error: err => console.log(err)
        })


    })
})

function delete_(e) {
    const parent = e.target.parentElement

    let length =  (parent.parentElement.querySelectorAll('.deleteBtn').length) - 1
    if (length === 0) {
        parent.parentElement.style.display = 'none'
    }
    parent.remove()
}