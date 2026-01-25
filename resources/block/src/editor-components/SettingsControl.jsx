import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const SettingsControl = ({attributes, setAttributes}) => {
    return (
        <InspectorControls>
            <PanelBody title={ __( 'Query Options', 'deep-search' ) }>
                <ToggleControl
                    label={ __( 'Show Post Type', 'deep-search' ) }
                    checked={attributes?.showPostType}
                    onChange={val => setAttributes({showPostType: val})}
                />
                <ToggleControl
                    label={ __( 'Show Category', 'deep-search' ) }
                    checked={attributes?.showCat}
                    onChange={val => setAttributes({showCat: val})}
                />
                <ToggleControl
                    label={ __( 'Show Tag', 'deep-search' ) }
                    checked={attributes?.showTag}
                    onChange={val => setAttributes({showTag: val})}
                />
            </PanelBody>
        </InspectorControls>
    )
}

export default SettingsControl;
