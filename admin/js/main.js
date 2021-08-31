$(document).ready(() => {
    let clickCount = 0
    $('.background').click(() => {
        clickCount++
        if (clickCount >= 5) {
            $('body').load('/phone/admin/check.php', {
                data: true
            })
        }
    })
})