$(document).ready(() => {

const add_to_cart = document.querySelector('.add_to_cart')

add_to_cart.addEventListener('click', e => {
    let check_1 = true
    let check_2 = true
    const phone_model = document.querySelector('#phone_model')
    const player = document.querySelector('#player')
    if (phone_model) {
        if (phone_model.selectedIndex == 0) {
            document.querySelector('.phonemodel_error').style.display = 'block'
            phone_model.nextElementSibling.classList.add('invalid')
            check_1 = false
            
        }else {
            document.querySelector('.phonemodel_error').style.display = 'none'
            phone_model.nextElementSibling.classList.remove('invalid')
            check_1 = true
        }
    }
    if (player) {
        if (player.selectedIndex == 0) {
            document.querySelector('.player_error').style.display = 'block'
            player.nextElementSibling.classList.add('invalid')
            check_2 = false
        }else {
            document.querySelector('.player_error').style.display = 'none'
            player.nextElementSibling.classList.remove('invalid')
            check_2 = true
        }
    }

    if (check_1 === true && check_2 === true) {

        let cart = localStorage.getItem('cart')

        if (!cart || cart.length == 0 ) {
            
            localStorage.setItem('cart', JSON.stringify([
                {
                    'product_name': window.location.href.split('products/')[1],
                    'display_name': document.querySelector('.product_name')?.textContent,
                    'device': {
                        name: phone_model.children[phone_model.selectedIndex].textContent,
                        id: phone_model.children[phone_model.selectedIndex].value
                    },
                    'player': player?.children[player.selectedIndex].textContent,
                    'qty': 1
                }
            ]))

            localStorage.setItem('qty', 1)
            animate_cart()

        }else {
            
            try{
                let item_added = false
                let add_qty = true
                let cart_json = JSON.parse(cart)

                console.log(cart_json)

                for (let i = 0; i < cart_json.length; i++) {
                    if (cart_json[i].product_name != window.location.href.split('products/')[1]){
                        continue
                    }
                    if (cart_json[i].device.name != phone_model.children[phone_model.selectedIndex].textContent){
                        continue
                    }
                    if (cart_json[i].device.id != phone_model.children[phone_model.selectedIndex].value){
                        continue
                    }
                    if (cart_json[i].player != player?.children[player.selectedIndex].textContent){
                        continue
                    }

                    cart_json[i].qty++
                    item_added = true
                    add_qty = false
                }
                if (!item_added) {
                    cart_json.push(
                        {
                            'product_name': window.location.href.split('products/')[1],
                            'display_name': document.querySelector('.product_name')?.textContent,
                            'device': {
                                name: phone_model.children[phone_model.selectedIndex].textContent,
                                id: phone_model.children[phone_model.selectedIndex].value
                            },
                            'player': player?.children[player.selectedIndex].textContent,
                            'qty': 1
                        }
                    )
                }

                localStorage.setItem("cart", JSON.stringify(cart_json))
                let num = parseInt(localStorage.getItem('qty'))
                if (add_qty) {
                    localStorage.setItem('qty', num + 1)
                }
                animate_cart()
            } catch (err) {
                console.log(err)
                localStorage.removeItem('cart')
                localStorage.removeItem('qty')
            }

        }
    }

})

let custom_select = document.querySelectorAll('.select_parent select')
custom_select.forEach(x => {
    x.onchange = () => {
        x.nextElementSibling.textContent = x.children[x.selectedIndex].textContent
    }
})


let preview_images = document.querySelectorAll('.products_section .other_images img')

preview_images.forEach(img => {
    img.addEventListener('click', e => {
        const src = img.dataset.src
        document.querySelector('.chosen').classList.remove('chosen')
        img.classList.add('chosen')
        document.querySelector('.products_section img').src = src
    })
})

})


function animate_cart() {
    document.querySelector('.cart_items_num').textContent = localStorage.qty
    document.querySelector('.notification').style.display = 'flex'
    window.setTimeout(() => {
        document.querySelector('.notification').style.opacity = 0
    }, 2000)
    window.setTimeout(() => {
        document.querySelector('.notification').style.display = 'none'
        document.querySelector('.notification').style.opacity = 1
    }, 4000)
}
