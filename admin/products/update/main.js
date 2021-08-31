let currentId;

$(document).ready(() => {

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



    $('form').submit(e => {
        e.preventDefault()
        search('clicked')
    })

    const searchBar = document.getElementById('searchBar')

    searchBar.addEventListener('keyup', e => {
        if (e.code == 'Enter') {
            search('clicked')
        }else {
            search('notClicked')
        }
    })

    const results = document.querySelector('form .results')

    function search(clicked) {
        if (!clicked) {
            return
        }

        $('form .results').load('./search.php', {
            searchBy: document.querySelector('#searchBy').options[document.querySelector('#searchBy').selectedIndex].value,
            name: searchBar.value.trim(),
            clicked
        });
    }
})

function loadData(e) {
    if (e.children.length !== 2)
        return
    
    if (!e.children[0].classList.contains('item-name') || !e.children[1].classList.contains('item-id'))
        return

    const name = e.children[0].textContent.trim()
    const id = e.children[1].textContent.trim()
    currentId = id
    $('#product').load('./loadProductData.php', {
        name,
        id
    })
    e.parentElement.style.display = 'none'
    document.getElementById('searchBar').value = ''
}