import { useState } from 'react';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const PostList = ({ props, queryRes, setQueryRes , setQueryData, queryData}) => {
    const [page, setPage] = useState(1)

    const handleClose = () => {
        setQueryData({})
        setQueryRes({})
    }

    const handlePagination = (currentPage) => {
        setQueryRes({loading: true})
        queryData['currentPage'] = currentPage
        setQueryData({...queryData})

        const form = new FormData()
        form.append('action', 'search');
        form.append('nonce', props.nonce);
        form.append('query', JSON.stringify(queryData));

        fetch(props.ajaxUrl, {
            method: 'POST',
            body: form,
        })
        .then((res => res.json()))
        .then(data => {
            setQueryRes(data?.data)
        })
        .catch(error => {
            console.error(error)
        })
    }

    return (
        <div className="ds-postlist">
            <div className="ds-postlist__close" onClick={handleClose}>
                <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px"><path d="M 4.7070312 3.2929688 L 3.2929688 4.7070312 L 10.585938 12 L 3.2929688 19.292969 L 4.7070312 20.707031 L 12 13.414062 L 19.292969 20.707031 L 20.707031 19.292969 L 13.414062 12 L 20.707031 4.7070312 L 19.292969 3.2929688 L 12 10.585938 L 4.7070312 3.2929688 z"/></svg>
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
                            >
                                {post.title}
                            </a>
                            <span className='ds-postlist__post-date'>{post.date}</span>
                        </div>
                    ))}

                    {(queryRes?.nextPage > 0 || queryRes?.prevPage > 0) &&
                        <div className='ds-postlist__pagination'>
                            {queryRes?.prevPage > 0 &&
                                <button
                                className='ds-postlist__pagination-btn'
                                onClick={() => handlePagination(queryRes?.prevPage)}
                                >
                                    {__('Previous', 'deep-search')}
                                </button>
                            }
                            {queryRes?.nextPage > 0 &&
                                <button
                                className='ds-postlist__pagination-btn'
                                onClick={() => handlePagination(queryRes?.nextPage)}
                                >
                                    {__('Next', 'deep-search')}
                                </button>
                            }
                        </div>
                    }
                    </>
                }
            </div>

            {queryRes?.posts?.length === 0 &&
                <div className='ds-postlist__inner'>
                    <p>{__('Posts not found', 'deep-search')}</p>
                </div>
            }
        </div>
    )
}

export default PostList;
