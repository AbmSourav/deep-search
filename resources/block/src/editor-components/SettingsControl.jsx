import {
    InspectorControls,
    PanelColorSettings,
    useSettings,
} from '@wordpress/block-editor'
import {
    BaseControl,
    FontSizePicker,
    PanelBody,
    ToggleControl,
    __experimentalSpacer as Spacer,
} from '@wordpress/components'
import { __ } from '@wordpress/i18n'

const SettingsControl = ({ attributes, setAttributes }) => {
    let fontSizes = useSettings('typography.fontSizes.theme')[0] ?? []

    if (fontSizes?.length === 0) {
        fontSizes = [
            {
                name: __('Small', 'deep-search'),
                slug: 'small',
                size: '22px',
            },
            {
                name: __('Medium', 'deep-search'),
                slug: 'medium',
                size: '30px',
            },
            {
                name: __('Large', 'deep-search'),
                slug: 'large',
                size: '40px',
            },
        ]
    }

    return (
        <>
            <InspectorControls group="settings">
                <PanelBody title={__('Query Options', 'deep-search')}>
                    <ToggleControl
                        label={__('Show Post Type', 'deep-search')}
                        checked={attributes?.showPostType}
                        onChange={(val) => setAttributes({ showPostType: val })}
                        __nextHasNoMarginBottom={true}
                    />
                    <ToggleControl
                        label={__('Show Category', 'deep-search')}
                        checked={attributes?.showCat}
                        onChange={(val) => setAttributes({ showCat: val })}
                        __nextHasNoMarginBottom
                    />
                    <ToggleControl
                        label={__('Show Tag', 'deep-search')}
                        checked={attributes?.showTag}
                        onChange={(val) => setAttributes({ showTag: val })}
                        __nextHasNoMarginBottom
                    />
                </PanelBody>
            </InspectorControls>

            <InspectorControls group="styles">
                <PanelBody title={__('General', 'deep-search')}>
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: attributes?.backgroundColor,
                                onChange: (colorValue) =>
                                    setAttributes({
                                        backgroundColor: colorValue,
                                    }),
                                label: __('Background Color', 'deep-search'),
                            },
                            {
                                value: attributes?.textColor,
                                onChange: (colorValue) =>
                                    setAttributes({ textColor: colorValue }),
                                label: __('Text Color', 'deep-search'),
                            },
                            {
                                value: attributes?.placeholderColor,
                                onChange: (colorValue) =>
                                    setAttributes({
                                        placeholderColor: colorValue,
                                    }),
                                label: __('Placeholder Color', 'deep-search'),
                            },
                        ]}
                    />
                </PanelBody>

                <PanelBody title={__('Options', 'deep-search')}>
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: attributes?.optionsBackgroundColor,
                                onChange: (colorValue) =>
                                    setAttributes({
                                        optionsBackgroundColor: colorValue,
                                    }),
                                label: __('Background Color', 'deep-search'),
                            },
                            {
                                value: attributes?.optionsTextColor,
                                onChange: (colorValue) =>
                                    setAttributes({
                                        optionsTextColor: colorValue,
                                    }),
                                label: __('Text Color', 'deep-search'),
                            },
                        ]}
                    />
                </PanelBody>

                <PanelBody title={__('Search Result', 'deep-search')}>
                    <PanelColorSettings
                        colorSettings={[
                            {
                                value: attributes?.postTitleColor,
                                onChange: (colorValue) =>
                                    setAttributes({
                                        postTitleColor: colorValue,
                                    }),
                                label: __('Post Color', 'deep-search'),
                            },
                            {
                                value: attributes?.postDateColor,
                                onChange: (colorValue) =>
                                    setAttributes({
                                        postDateColor: colorValue,
                                    }),
                                label: __('Date Color', 'deep-search'),
                            },
                        ]}
                    />

                    <Spacer marginTop={4}>
                        <BaseControl
                            className="ds-font-size"
                            style={{ marginTop: '15px' }}
                            __nextHasNoMarginBottom={true}
                        >
                            <p>{__('Title Font Size', 'deep-search')}</p>
                            <FontSizePicker
                                fontSizes={fontSizes}
                                fallbackFontSize={30}
                                value={attributes?.postTitleFontSize}
                                onChange={(fontSize) =>
                                    setAttributes({
                                        postTitleFontSize: fontSize,
                                    })
                                }
                                className={'testsss'}
                                __next40pxDefaultSize={true}
                            />
                        </BaseControl>
                    </Spacer>

                    <Spacer marginTop={8}>
                        <PanelColorSettings
                            title={__('Pagination', 'deep-search')}
                            colorSettings={[
                                {
                                    value: attributes?.paginationBtnBg,
                                    onChange: (colorValue) =>
                                        setAttributes({
                                            paginationBtnBg: colorValue,
                                        }),
                                    label: __(
                                        'Background Color',
                                        'deep-search'
                                    ),
                                },
                                {
                                    value: attributes?.paginationBtnColor,
                                    onChange: (colorValue) =>
                                        setAttributes({
                                            paginationBtnColor: colorValue,
                                        }),
                                    label: __('Color', 'deep-search'),
                                },
                            ]}
                        />
                    </Spacer>
                </PanelBody>
            </InspectorControls>
        </>
    )
}

export default SettingsControl
