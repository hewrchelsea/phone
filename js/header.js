$(document).ready(() => {

    let burger_status = 'closed'

    const burger = document.querySelector('.burger')
    const nav_parent = document.querySelector('.nav_parent')
    burger.onclick = () => {
        if (burger_status == 'closed') {
            //Open the menu
            burger_status = 'opened'
            nav_parent.style.display = 'flex'
            burger.children[0].style.transform = 'rotate(135deg)'
            burger.children[0].style.width = '100%'
            burger.children[0].style.top = '50%'
            burger.children[0].style.bottom = '0'
            
            burger.children[1].style.transform = 'rotate(-135deg)'
            burger.children[1].style.width = '100%'
            burger.children[1].style.top = '50%'
            burger.children[1].style.bottom = '0'

            document.body.style.overflow= 'hidden'
        }else {
            //close the menu
            nav_parent.style.display = 'none'
            burger.children[0].style.transform = ''
            burger.children[0].style.width = ''
            burger.children[0].style.top = '5px'
            burger.children[0].style.bottom = ''
            
            burger.children[1].style.transform = ''
            burger.children[1].style.width = ''
            burger.children[1].style.top = ''
            burger.children[1].style.bottom = '5px'
            burger_status = 'closed'
            document.body.style.overflow= ''

        }
    }
    if (localStorage.getItem('qty')) {
        try{
            const cart = JSON.parse(localStorage.getItem('cart'))

            if (cart.length === 0) {
                document.querySelector('.cart_items_num').style.display = 'none'
            }
            document.querySelector('.cart_items_num').textContent = cart.length
        } catch (err) {
            localStorage.clear()
            document.querySelector('.cart_items_num').style.display = 'none'
        }  
    }
})