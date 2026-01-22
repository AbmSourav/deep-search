import { createRoot } from '@wordpress/element';

import Admin from './Admin';
import './style.scss';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#ds-container');
    console.log('container', container)

    if (container) {
        const root = createRoot(container);
        root.render(<Admin />);
    } else {
        console.error('Container #ds-container not found!');
    }
});
