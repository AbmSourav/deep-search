console.log('ds block loaded...')

document.addEventListener('DOMContentLoaded', () => {
    const dsRoot = document.querySelector('.ds-root')
    const searchBar = document.querySelector('.ds-bar')
    const searchInput = document.querySelector('.ds-bar .ds-input')
    const searchIcon = document.querySelector('.ds-bar .ds-icon__search')
    const clearIcon = document.querySelector('.ds-bar .ds-icon__clear')
    const searchOption = document.querySelector('.ds-options')
    console.log('search input', searchOption.clientHeight, dsRoot.offsetHeight)

    searchInput.addEventListener('focusin', () => {
        dsRoot.classList.add('active')
        dsRoot.style.height = searchBar.offsetHeight + 'px'
        // searchOption.style.position = 'absolute'
        searchOption.classList.add('show')
    })
    searchInput.addEventListener('blur', () => {
        dsRoot.style.height = 'auto'
        dsRoot.classList.remove('active')
        searchOption.classList.remove('show')
    })

    searchInput.addEventListener('input', (event) => {
        console.log('input', event.target.value)
        if (event.target.value) {
            searchIcon.classList.add('hide')
            clearIcon.classList.add('show')
        } else {
            clearIcon.classList.remove('show')
            searchIcon.classList.remove('hide')
        }
    })

    clearIcon.addEventListener('click', (event) => {
        event.preventDefault()

        searchInput.focus()
        searchInput.value = ''
        clearIcon.classList.remove('show')
        searchIcon.classList.remove('hide')
    })
})
