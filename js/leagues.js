
    const profile_parent = document.querySelector('.profile_parent')
    const closeBtn = document.querySelector('.profile .closeBtn')
    window.addEventListener('click', e => {
        if (e.target.classList.contains('profile_parent')) {
            e.target.remove()
            document.body.style.overflow = ''
        }
    })
    if (closeBtn) {
        closeBtn.onclick = () => {
            profile_parent.remove()
            document.body.style.overflow = ''
        }
    }

    window.setTimeout(() => {
        if (profile_parent) {
            profile_parent.style.display = 'flex'
            document.body.style.overflow = 'hidden'
        }
    }, 50000)
