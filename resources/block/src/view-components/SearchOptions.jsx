import Select from 'react-select';

const SearchOptions = ({ props, queryData, setQueryData }) => {
    const { postTypes, categories, tags } = props

    const handlePostTypleChange = (postTypes) => {
        let postTypesData = ''
        postTypes.map(postType => {
            if (!postTypesData) {
                return postTypesData = postType?.value
            }
            postTypesData += ', ' + postType?.value
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

    return (
        <div className="ds-options">
            {console.log('changes>>', queryData)}
            {postTypes.length &&
                <div className="ds-options__pt">
                    <Select
                    className="ds-options__pt-select"
                    classNamePrefix="ds"
                    placeholder="Post type"
                    options={postTypes}
                    isMulti={true}
                    onChange={handlePostTypleChange}
                    />
                </div>
            }

            {categories.length &&
                <div className="ds-options__cat">
                    <Select
                    className="ds-options__cat-select"
                    classNamePrefix="ds"
                    placeholder="Category"
                    options={categories}
                    isMulti={true}
                    onChange={handleCatChange}
                    />
                </div>
            }

            {tags.length &&
                <div className="ds-options__tag">
                    <Select
                    className="ds-options__tag-select"
                    classNamePrefix="ds"
                    placeholder="Tag"
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
