import { useRef, useState, useEffect } from 'react'
import Select from 'react-select'
import createCache from '@emotion/cache'
import { CacheProvider } from '@emotion/react'
import { __ } from '@wordpress/i18n'

import { adjustColor } from '../helper'

const SearchOptions = ({ props, queryState }) => {
    const { postTypes, categories, tags, attibutes } = props
    const containerRef = useRef(null)
    const [emotionCache, setEmotionCache] = useState(null)
    const { queryData, setQueryData } = queryState

    useEffect(() => {
        if (containerRef.current) {
            setEmotionCache(
                createCache({
                    key: 'ds-select',
                    container: containerRef.current.ownerDocument.head,
                })
            )
        }
    }, [])

    const handlePostTypleChange = (postTypes) => {
        let postTypesData = ''
        postTypes.map((postType) => {
            postTypesData += postType.value + ','
        })

        queryData.postTypes = postTypesData.replace(/,*\s*$/, '')
        setQueryData({ ...queryData })
    }

    const handleCatChange = (cats) => {
        let catData = ''
        cats.map((cat) => {
            catData += cat?.term_id + ','
        })

        queryData.cats = catData.replace(/,*\s*$/, '')
        setQueryData({ ...queryData })
    }

    const handleTagChange = (tags) => {
        let tagData = ''
        tags.map((tag) => {
            tagData += tag.term_id + ','
        })

        queryData.tags = tagData.replace(/,*\s*$/, '')
        setQueryData({ ...queryData })
    }

    const optionsStyles = {
        control: (baseStyles) => ({
            ...baseStyles,
            backgroundColor:
                adjustColor(attibutes?.optionsBackgroundColor) + '!important' ||
                '#fff',
            borderColor:
                attibutes?.optionsBackgroundColor + '!important' || '#fff',
            boxShadow: attibutes?.optionsBackgroundColor
                ? `0 0 0 1px ${attibutes?.optionsBackgroundColor}` +
                  '!important'
                : '0 0 0 1px #b4b4b4',
            color: attibutes?.optionsTextColor + '!important' || '#959595',
        }),
        option: (styles, { isFocused }) => ({
            ...styles,
            backgroundColor: isFocused
                ? adjustColor(attibutes?.optionsBackgroundColor) || '#83aeeb78'
                : attibutes?.optionsBackgroundColor || '#fff',
            color: attibutes?.optionsTextColor || 'initial',
            ':active': {
                ...styles[':active'],
                backgroundColor:
                    adjustColor(attibutes?.optionsBackgroundColor) || 'initial',
            },
        }),
        multiValue: (styles) => {
            return {
                ...styles,
                backgroundColor: attibutes?.optionsBackgroundColor || '#ddd',
            }
        },
        multiValueLabel: (styles) => ({
            ...styles,
            color: attibutes?.optionsTextColor || '#',
        }),
    }

    if (
        postTypes?.length === 0 &&
        categories?.length === 0 &&
        tags?.length === 0
    ) {
        return
    }

    return (
        <>
            <style>{`
            .ds__placeholder {color: ${
                attibutes?.optionsTextColor || 'inherit'
            };}
            .ds__menu {background-color: ${
                attibutes?.optionsBackgroundColor || '#fff'
            };}
            .ds-options {border-top-color: ${
                adjustColor(attibutes?.backgroundColor) || '#e2e2e2'
            } !important;}
            `}</style>

            <div className="ds-options" ref={containerRef}>
                {emotionCache ? (
                    <CacheProvider value={emotionCache}>
                        {postTypes?.length > 0 && (
                            <div className="ds-options__pt">
                                <Select
                                    className="ds-options__pt-select"
                                    classNamePrefix="ds"
                                    placeholder={__('Post type', 'deep-search')}
                                    options={postTypes}
                                    isMulti={true}
                                    onChange={handlePostTypleChange}
                                    styles={optionsStyles}
                                />
                            </div>
                        )}

                        {categories?.length > 0 && (
                            <div className="ds-options__cat">
                                <Select
                                    className="ds-options__cat-select"
                                    classNamePrefix="ds"
                                    placeholder={__('Category', 'deep-search')}
                                    options={categories}
                                    isMulti={true}
                                    onChange={handleCatChange}
                                    styles={optionsStyles}
                                />
                            </div>
                        )}

                        {tags?.length > 0 && (
                            <div className="ds-options__tag">
                                <Select
                                    className="ds-options__tag-select"
                                    classNamePrefix="ds"
                                    placeholder={__('Tag', 'deep-search')}
                                    options={tags}
                                    isMulti={true}
                                    onChange={handleTagChange}
                                    styles={optionsStyles}
                                />
                            </div>
                        )}
                    </CacheProvider>
                ) : null}
            </div>
        </>
    )
}

export default SearchOptions
