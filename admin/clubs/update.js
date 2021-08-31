$(document).ready(() => {

    let label = document.querySelectorAll('label')
    label.forEach(l => {
        l.addEventListener('click', e => {
            e.preventDefault()
        })
    })


    let input = document.querySelector('input')
    input.addEventListener('keyup', e => {
        search(input.value.trim(), 'notClicked')
    })

    let search_btn = document.querySelector('.search')
    search_btn.addEventListener('click', e => {
        e.preventDefault()
        search(input.value.trim(), 'clicked')
    })


    function search(name, click) {
        
        document.querySelector('.result').style.display = 'flex'
        $('.result').load('./search.php', {
            name,
            click
        })
    
    }
})

function loadData(e) {
    if (!'id' in e.dataset) {
        return false
    }
    if (e.tagName === 'DIV') {
        let all = document.querySelectorAll('.chosen_club')
        for (let i = 0; i < all.length; i++) {
            all[i].classList.remove('chosen_club')
        }
        e.classList.add('chosen_club')
    }


    $('.club').css('display', 'flex')
    $('.club').load('loadclub.php', {
        id: e.dataset.id
    })
}