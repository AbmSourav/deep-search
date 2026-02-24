import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

import SettingsControl from './editor-components/SettingsControl';
import DeepSearch from './view-components/DeepSearch';

export default function Edit( { attributes, setAttributes } ) {
    const blockProps = useBlockProps({
        className: 'ds-block-editor'
    });

    const { showPostType, showCat, showTag } = attributes

    const postTypes = useSelect((select) => {
        if (!showPostType) return [];
        const types = select(coreStore).getPostTypes({ per_page: -1, show_ui: true }) || [];
        return types
            .filter(type => type.viewable && type.slug !== 'attachment')
            .map(type => ({ value: type.slug, label: type.name }));
    }, [showPostType]);

    const categories = useSelect((select) => {
        if (!showCat) return [];
        const cats = select(coreStore).getEntityRecords('taxonomy', 'category', { per_page: 5, hide_empty: true }) || [];
        return cats.map(cat => ({ term_id: cat.id, value: cat.slug, label: cat.name }));
    }, [showCat]);

    const tags = useSelect((select) => {
        if (!showTag) return [];
        const tagList = select(coreStore).getEntityRecords('taxonomy', 'post_tag', { per_page: 5, hide_empty: true }) || [];
        return tagList.map(tag => ({ term_id: tag.id, value: tag.slug, label: tag.name }));
    }, [showTag]);

    const deepSearchProps = {
        attibutes: attributes,
        postTypes,
        categories,
        tags,
    };

    return (
        <div {...blockProps}>
            <SettingsControl attributes={attributes} setAttributes={setAttributes} />

            <DeepSearch props={deepSearchProps} />
        </div>
    );
}
