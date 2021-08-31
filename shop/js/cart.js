$(document).ready(() => {

    if (localStorage.getItem('cart')?.length > 0) {
        $.ajax('/phone/shop/cart_check.php', {
            method: "POST",
            data: {
                "data": localStorage.cart,
                "method": "return"
            },
            success: data => {

                try{
                    if (data.length === 0) {
                        throw new error();
                    }
                    let json = JSON.parse(data)
                    let total = 0
                    for (let i = 0; i < json.length; i++) {
                        const j = json[i]

                        const obj = JSON.parse(j)
                        
                        const item_card = document.createElement('div')
                        item_card.classList.add('item_card')
                        
                        const left = document.createElement('div')
                        left.classList.add('left')
                        
                        const img = document.createElement('img')
                        img.src = `/phone/assets/mockups/156/${obj.img_src}`
                        
                        left.appendChild(img)
                        
                        const right = document.createElement('div')
                        right.classList.add('right')
                        
                        
                        
                        const title = document.createElement('h2')
                        title.classList.add('title')
                        title.appendChild(document.createTextNode(`${obj.product_name}`))
                        
                        const sub_title = document.createElement('div')
                        sub_title.classList.add('sub_title')
                        sub_title.classList.add('player')
                        sub_title.textContent = `Player: ${obj.player}`
                        const phone_model = document.createElement('div')
                        phone_model.classList.add('phone_model')
                        phone_model.classList.add('sub_title')
                        phone_model.appendChild(document.createTextNode(obj.product_name))
                        phone_model.textContent = `Phone Model: ${obj.device_name}`
                        
                        const input = document.createElement('div')
                        input.classList.add('input')
                        
                        
                        
                        const minus = document.createElement('span')
                        minus.classList.add('minus')
                        minus.appendChild(document.createTextNode('-'))
                        
                        let input_field = document.createElement('input')
                        input_field.type = 'text'
                        input_field.value = parseInt(obj.qty)
                        
                        
                        
                        const plus = document.createElement('span')
                        plus.classList.add('plus')
                        plus.appendChild(document.createTextNode('+'))
                        
                        const price = document.createElement('div')
                        price.classList.add('price')
                        price.appendChild(document.createTextNode(`$${obj.price} USD`))
                        
                        input.appendChild(minus)
                        input.appendChild(input_field)
                        input.appendChild(plus)
                        
                        
                        right.appendChild(title)
                        right.appendChild(sub_title)
                        right.appendChild(phone_model)
                        right.appendChild(input)
                        right.appendChild(price)
                        
                        const img_icon = document.createElement('img')
                        img_icon.classList.add('deleteBtn')
                        img_icon.onclick = deleteItem
                        img_icon.src = '/phone/images/icons/delete.png'
                        img_icon.alt = 'Delete Item'
                        
                        item_card.appendChild(left)
                        item_card.appendChild(right)
                        item_card.appendChild(img_icon)
                        //Add the data attribute
                        item_card.dataset.name = obj.product_name
                        item_card.dataset.player = obj.player
                        item_card.dataset.phone_model = obj.device_name
                        item_card.dataset.price = obj.price
                        document.querySelector('.cart_content').appendChild(item_card)
                        
                        total += parseFloat(obj.price)

                        if (i === (json.length - 1)) {
                            const total_elt = document.createElement('div')
                            total_elt.classList.add('total')
                            total_elt.appendChild(document.createTextNode(`Total: $${total.toFixed(2)} USD`))
                            total_elt.dataset.value = total.toFixed(2)
                            
                            const checkout_btn = document.createElement('button')
                            checkout_btn.classList.add('checkout')
                            checkout_btn.type = 'submit'
                            checkout_btn.appendChild(document.createTextNode('CHECKOUT'))

                            document.querySelector('.cart_content').appendChild(total_elt)
                            document.querySelector('.cart_content').appendChild(checkout_btn)
                        }

                    }


                }catch (err) {
                    console.log(err)
                }
    
            },
            error: data => {
                console.log(data.responseText)
                // 
                // localStorage.clear()
                // document.querySelector('.cart_items_num').style.display = 'none'
                // document.querySelector('.empty').style.display = 'flex'
            }
        })
    }else {
        console.log('Cart is empty')
        document.querySelector('.empty').style.display = 'flex'
    }
})

function deleteItem(e) {
    if (typeof e != 'object') {
        return
    }
    const parent = e.target.parentElement
    const data = {
        display_name: parent.dataset.name,
        player_name: parent.dataset.player,
        phone_model: parent.dataset.phone_model
    }
    
    
    try{
        
        let cart = JSON.parse(localStorage.getItem('cart'))
        let total = parseFloat(document.querySelector('.total').dataset.value)


        for (let i = 0; i < cart.length; i++) {
            if (cart[i].display_name != data.display_name)
                continue
            if (cart[i].player != data.player_name)
                continue
            if (cart[i].device.name != data.phone_model)
                continue

            cart.splice(i, 1)
            localStorage.setItem('cart', JSON.stringify(cart))
            localStorage.setItem('qty', parseInt(localStorage.getItem('qty')) - 1)
            document.querySelector('.cart_items_num').textContent = localStorage.getItem('qty')
            parent.style.opacity = 0
            setTimeout(() => {
                let price_ = parent.dataset.price
                total = total - price_
                document.querySelector('.total').textContent = `Total: $${total.toFixed(2)} USD`
                document.querySelector('.total').dataset.value = total
                parent.remove()
                if (cart.length === 0) {
                    document.querySelector('.total').remove()
                    document.querySelector('.checkout').remove()
                    document.querySelector('.cart_items_num').style.display = 'none'
                    document.querySelector('.empty').style.display = 'flex'
                } 
            }, 1000)
            break
        }


    }catch (err) {
        console.log('There is an error')
        //something wrong with the cart
        //delete the cart
        // localStorage.clear()
        // window.location.reload()
    }
}