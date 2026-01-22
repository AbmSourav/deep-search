import { createRoot } from '@wordpress/element';

import DeepSearch from './view-components/DeepSearch';

document.querySelectorAll('.ds-root').forEach((container) => {
    const blockProps = JSON.parse(container.dataset?.block)
    const root = createRoot(container);
    root.render(<DeepSearch props={blockProps} />);
});
