$(document).ready(() => {



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
        const msg = document.createElement('p')
        msg.classList.add('msg')

        const deleteBtn = document.createElement('button')
        deleteBtn.classList.add('deleteBtn')
        deleteBtn.type = 'button'
        deleteBtn.appendChild(document.createTextNode('Delete'))
        deleteBtn.addEventListener('click', e => delete_(e))
        card.appendChild(card_img)
        card.appendChild(variant_name)
        card.appendChild(msg)
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
        document.querySelector('.mockups .upload_mockup .error').textContent = ''

        if (!file) {
            errorCheck = true
            window.setTimeout(() => {
                document.querySelector('.mockups .upload_mockup .error').textContent = 'Please upload a file'
                document.querySelector('.mockups .upload_mockup .custom_input_file').classList.add('invalid')
            }, 100)
            return  
        }
        if (file.type.indexOf('image/') < 0){
            errorCheck = true
            window.setTimeout(() => {
                document.querySelector('.mockups .upload_mockup .error').textContent = 'The file type you uploaded is not allowed. Please upload an image'
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
        const msg = document.createElement('p')
        msg.classList.add('msg')

        const deleteBtn = document.createElement('button')
        deleteBtn.classList.add('deleteBtn')
        deleteBtn.type = 'button'
        deleteBtn.appendChild(document.createTextNode('Delete'))
        deleteBtn.addEventListener('click', e => delete_(e))
        card.appendChild(card_img)
        card.appendChild(name)
        card.appendChild(msg)
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

        phone_model_input.parentElement.parentElement.querySelector('.error').textContent = ''
        phone_model_input.nextElementSibling.classList.remove('invalid')

        device_id.classList.remove('invalid')
        device_id.parentElement.querySelector('.error').textContent = ''



        let errorCheck = false

        if (phone_model_input.selectedIndex === 0) {
            
            errorCheck = true
            window.setTimeout(() => {
                document.querySelector('.phone_list .input .custom_select').classList.add('invalid')
                document.querySelectorAll('.phone_list .input .error')[0].textContent = 'Please choose a phone model.'
            }, 100)
        }

        if (device_id.value.trim().length === 0) {

            errorCheck = true
            window.setTimeout(() => {
                device_id.classList.add('invalid')
                device_id.parentElement.querySelector('.error').textContent = 'Please fill in this input.'
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
                    }, 100)
                }
                if (phone_elt[i].children[1].textContent.toLowerCase() == device_id.value.trim().toLowerCase()) {
                    check = true
                    window.setTimeout(() => {
                        device_id.classList.add('invalid')
                        device_id.parentElement.querySelector('.error').textContent = 'A phone model with this ID already exist. Please delete it first.'
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
        const msg = document.createElement('p')
        msg.classList.add('msg')
        const delete_btn = document.createElement('button')
        delete_btn.classList.add('deleteBtn')
        delete_btn.type = 'button'
        delete_btn.addEventListener('click', e => delete_(e))

        btn.appendChild(device_name)
        btn.appendChild(device_id_elt)
        btn.appendChild(delete_btn)
        btn.appendChild(msg)
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
            
            if (e.target.value.trim().length > 0) {
                e.target.classList.remove('invalid')
                e.target.parentElement.querySelector('.error').textContent = ''
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
            url: './add.php',
            method: "POST",
            data: {
                product_name: document.getElementById('product_name').value,
                product_price: document.getElementById('product_price').value,
                discount: document.getElementById('discount').value,
                league: document.getElementById('league').options[document.getElementById('league').selectedIndex].value || '',
                club: document.getElementById('club').options[document.getElementById('club').selectedIndex]?.value || '',
                collection: document.getElementById('collection').options[document.getElementById('collection').selectedIndex]?.value || '',
                phones: JSON.stringify(added_devices),
                variants: JSON.stringify(added_variants),
                mockups: JSON.stringify(added_mockups),
                mockups_92: JSON.stringify(mockups_92),
                mockup_156: JSON.stringify(added_mockups[0]) || ""
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
                url: './leagueCheck.php',
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


})

function delete_(e) {
    const parent = e.target.parentElement

    let length =  (parent.parentElement.querySelectorAll('.deleteBtn').length) - 1
    if (length === 0) {
        parent.parentElement.style.display = 'none'
    }
    parent.remove()
}