window.onload = () => {
    document.querySelector('.close')?.addEventListener('click', e => {
        document.querySelector('.close').parentElement.style.display = 'none'
    })
    const filters = document.querySelector('.filters_popup')
    const closeBtn = filters?.querySelector('.header .left')
    if (closeBtn){
        closeBtn.onclick = close
    }
    document.addEventListener('click', event => {
        if (event.target.classList.contains('filters_popup')){
            event.target.style.display = 'none'
            document.body.style.overflow = 'auto'
        }
    })

    function close() {
        filters.style.display = 'none'
        document.body.style.overflow = 'auto'
    }
    const filter_btn = document.querySelector('.filter')
    if (filter_btn) {
        filter_btn.onclick = () => {
            filters.style.display = 'flex'
            document.body.style.overflow = 'hidden'
        }
    }
    
    let select = document.querySelectorAll('select') 
    for (let i = 0; i < select.length; i++) {
        select[i].addEventListener('change', e => {
            select[i].nextElementSibling.textContent = select[i][select[i].selectedIndex].textContent

            if (select[i].selectedIndex > 0) {
                select[i].nextElementSibling.classList.remove('invalid')
                select[i].parentElement.parentElement.querySelector('.error').style.display = 'none'
            }
        })
    }
}