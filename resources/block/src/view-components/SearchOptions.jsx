import Select from 'react-select';
import { __ } from '@wordpress/i18n';

const SearchOptions = ({ props, queryData, setQueryData }) => {
    const { postTypes, categories, tags } = props

    const handlePostTypleChange = (postTypes) => {
        let postTypesData = []
        postTypes.map(postType => {
            postTypesData.push(postType.value)
        })

        queryData.postTypes = postTypesData
        setQueryData({...queryData})
    }

    const handleCatChange = (cats) => {
        let catData = []
        cats.map(cat => {
            catData.push(cat?.term_id)
        })

        queryData.cats = catData
        setQueryData({...queryData})
    }

    const handleTagChange = (tags) => {
        let tagData = []
        tags.map(tag => {
            tagData.push(tag.term_id)
        })

        queryData.tags = tagData
        setQueryData({...queryData})
    }

    if (
        postTypes?.length === 0 &&
        categories?.length === 0 &&
        tags?.length === 0
    ) {
        return
    }

    return (
        <div className="ds-options">
            {postTypes?.length > 0 &&
                <div className="ds-options__pt">
                    <Select
                    className="ds-options__pt-select"
                    classNamePrefix="ds"
                    placeholder={__("Post type", "deep-search")}
                    options={postTypes}
                    isMulti={true}
                    onChange={handlePostTypleChange}
                    />
                </div>
            }

            {categories?.length > 0 &&
                <div className="ds-options__cat">
                    <Select
                    className="ds-options__cat-select"
                    classNamePrefix="ds"
                    placeholder={__("Category", "deep-search")}
                    options={categories}
                    isMulti={true}
                    onChange={handleCatChange}
                    />
                </div>
            }

            {tags?.length > 0 &&
                <div className="ds-options__tag">
                    <Select
                    className="ds-options__tag-select"
                    classNamePrefix="ds"
                    placeholder={__("Tag", "deep-search")}
                    options={tags}
                    isMulti={true}
                    onChange={handleTagChange}
                    />
                </div>
            }
        </div>
    )
}

export default SearchOptions;
