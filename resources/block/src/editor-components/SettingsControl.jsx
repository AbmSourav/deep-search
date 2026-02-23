import { InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const SettingsControl = ({attributes, setAttributes}) => {
    return (
        <>
            <InspectorControls group="settings">
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

            <InspectorControls group="styles">
                <PanelBody title={ __( 'General', 'deep-search' ) }>
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: attributes?.backgroundColor,
                                onChange: (colorValue) => setAttributes({ backgroundColor: colorValue }),
                                label: __('Background Color'),
                            },
                            {
                                value: attributes?.textColor,
                                onChange: (colorValue) => setAttributes({ textColor: colorValue }),
                                label: __('Text Color'),
                            },
                            {
                                value: attributes?.placeholderColor,
                                onChange: (colorValue) => setAttributes({ placeholderColor: colorValue }),
                                label: __('Placeholder Color'),
                            }
                        ]}
                    />
                </PanelBody>

                <PanelBody title={ __( 'Options', 'deep-search' ) }>
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: attributes?.optionsBackgroundColor,
                                onChange: (colorValue) => setAttributes({ optionsBackgroundColor: colorValue }),
                                label: __('Background Color'),
                            },
                            {
                                value: attributes?.optionsTextColor,
                                onChange: (colorValue) => setAttributes({ optionsTextColor: colorValue }),
                                label: __('Text Color'),
                            }
                        ]}
                    />
                </PanelBody>

                <PanelBody title={ __( 'Search Result', 'deep-search' ) }>
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: attributes?.postTitleColor,
                                onChange: (colorValue) => setAttributes({ postTitleColor: colorValue }),
                                label: __('Post Color'),
                            },
                            {
                                value: attributes?.postDateColor,
                                onChange: (colorValue) => setAttributes({ postDateColor: colorValue }),
                                label: __('Date Color'),
                            }
                        ]}
                    />
                </PanelBody>
            </InspectorControls>
        </>
    )
}

export default SettingsControl;
