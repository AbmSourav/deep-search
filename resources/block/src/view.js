import { createRoot } from '@wordpress/element';

import DeepSearch from './view-components/DeepSearch';

document.querySelectorAll('.ds-root').forEach((container) => {
    const blockProps = JSON.parse(container.dataset?.block)
    const root = createRoot(container);
    root.render(<DeepSearch props={blockProps} />);
});

// document.addEventListener('DOMContentLoaded', () => {
//     const dsRoot = document.querySelector('.ds-root')
//     const dsWrap = document.querySelector('.ds-wrap')
//     const searchBar = document.querySelector('.ds-bar')
//     const searchInput = document.querySelector('.ds-bar .ds-input')
//     const searchIcon = document.querySelector('.ds-bar .ds-icon__search')
//     const clearIcon = document.querySelector('.ds-bar .ds-icon__clear')
//     const searchOption = document.querySelector('.ds-options')
//     const searchOptionHeight = searchOption.offsetHeight

//     searchInput.addEventListener('focusin', () => {
//         dsRoot.classList.add('active')
//         dsWrap.style.height = searchBar.offsetHeight + searchOptionHeight + 'px'
//         searchOption.classList.add('show')
//     })

//     document.addEventListener('click', (event) => {
//         const isInsideRoot = event.target.closest('.ds-wrap')
//         console.log('closest::', isInsideRoot, event.target.closest('.ds-options'))

//         if (dsRoot.classList.contains('active') && !isInsideRoot) {
//             dsRoot.style.height = 'auto'
//             dsRoot.classList.remove('active')
//             searchOption.classList.remove('show')
//         }
//     })

//     searchInput.addEventListener('input', (event) => {
//         console.log('input', event.target.value)
//         if (event.target.value) {
//             searchIcon.classList.add('hide')
//             clearIcon.classList.add('show')
//         } else {
//             clearIcon.classList.remove('show')
//             searchIcon.classList.remove('hide')
//         }
//     })

//     clearIcon.addEventListener('click', (event) => {
//         event.preventDefault()

//         searchInput.focus()
//         searchInput.value = ''
//         clearIcon.classList.remove('show')
//         searchIcon.classList.remove('hide')
//     })
// })
