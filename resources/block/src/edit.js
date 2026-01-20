import { useBlockProps } from '@wordpress/block-editor';

export default function Edit( { attributes, setAttributes } ) {
    const blockProps = useBlockProps({
        className: 'ds-block-editor'
    });

    return (
        <div {...blockProps}>
            <div className='ds-block'>
                Hello
            </div>
        </div>
    );
}
