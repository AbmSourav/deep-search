import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import { ajaxRequest, restApiRequest, adjustColor } from '../helper';

const PostList = ({ props, resData, queryState, rest}) => {
    const { attibutes } = props
    const { queryData, setQueryData } = queryState
    const { isRestDisabled, setIsRestDisabled } = rest
    const { queryRes, setQueryRes } = resData

    const handleClose = () => {
        setQueryData({})
        setQueryRes({})
    }

    const handlePagination = async (currentPage) => {
        setQueryRes({loading: true})
        queryData['currentPage'] = currentPage
        setQueryData({...queryData})
        let resData = null
        let restFailed = isRestDisabled

        if (restFailed === false) {
            const restRes = await restApiRequest(queryData)
            if (restRes?.status === 200) {
                resData = await restRes.json()
            } else if (restRes?.status > 399) {
                restFailed = true
                setIsRestDisabled(true)
            }
        }

        // if rest-api is not enabled then do the ajax request
        if (restFailed) {
            const ajaxRes = await ajaxRequest(props, queryData)
            if (ajaxRes?.status === 200) {
                resData = await ajaxRes.json()
            }
        }

        setQueryRes(resData?.data)
    }

    const paginationStyles = {
        backgroundColor: attibutes?.paginationBtnBg || 'initial',
        color: attibutes?.paginationBtnColor || 'initial',
        borderColor: adjustColor(attibutes?.paginationBtnBg) || 'initial'
    }

    return (
        <div className="ds-postlist">
            <style>{
                `
                .ds-postlist__post {border-bottom-color: ${adjustColor(attibutes?.backgroundColor) || '#eee'} !important;}
                .ds-postlist__inner {scrollbar-color: #b4b4b4 ${adjustColor(attibutes?.backgroundColor) || '#e3e3e37c'} !important;}
                `
            }</style>

            <div className="ds-postlist__header" style={{backgroundColor: adjustColor(attibutes?.backgroundColor) || '#eee'}}>
                <div className="ds-postlist__header-close" onClick={handleClose}>
                    <svg style={{fill: adjustColor(attibutes?.textColor)}} xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px"><path d="M 4.7070312 3.2929688 L 3.2929688 4.7070312 L 10.585938 12 L 3.2929688 19.292969 L 4.7070312 20.707031 L 12 13.414062 L 19.292969 20.707031 L 20.707031 19.292969 L 13.414062 12 L 20.707031 4.7070312 L 19.292969 3.2929688 L 12 10.585938 L 4.7070312 3.2929688 z"/></svg>
                </div>
            </div>

            <div className='ds-postlist__inner'>
                {queryRes?.loading &&
                    <div className='ds-postlist__loader'>
                        <Spinner />
                    </div>
                }

                {queryRes?.posts?.length > 0 &&
                    <>
                    {queryRes.posts.map((post, index) => (
                        <div className='ds-postlist__post'>
                            <a
                            href={post.permalink}
                            key={index}
                            className='ds-postlist__post-title'
                            style={{color: attibutes?.postTitleColor || '#027141', fontSize: attibutes?.postTitleFontSize || '30px'}}
                            >
                                {post.title}
                            </a>
                            <span
                            className='ds-postlist__post-date'
                            style={{color: attibutes?.postDateColor || '#696969'}}
                            >{post.date}</span>
                        </div>
                    ))}

                    {(queryRes?.nextPage > 0 || queryRes?.prevPage > 0) &&
                        <div className='ds-postlist__pagination'>
                            {queryRes?.prevPage > 0 &&
                                <button
                                className='ds-postlist__pagination-btn'
                                onClick={() => handlePagination(queryRes?.prevPage)}
                                style={paginationStyles}
                                >
                                    {__('Previous', 'deep-search')}
                                </button>
                            }
                            {queryRes?.nextPage > 0 &&
                                <button
                                className='ds-postlist__pagination-btn'
                                onClick={() => handlePagination(queryRes?.nextPage)}
                                style={paginationStyles}
                                >
                                    {__('Next', 'deep-search')}
                                </button>
                            }
                        </div>
                    }
                    </>
                }

                {queryRes?.posts?.length === 0 &&
                    <p>{__('Nothing found', 'deep-search')}</p>
                }
            </div>

        </div>
    )
}

export default PostList;
