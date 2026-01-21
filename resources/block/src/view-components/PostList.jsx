import { Spinner } from '@wordpress/components';

const PostList = ({ queryRes, setQueryRes , setQueryData}) => {

    const handleClose = () => {
        setQueryData({})
        setQueryRes({})
    }

    return (
        <div className="ds-postlist">
            <div className="ds-postlist__close" onClick={handleClose}>
                <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" width="24px" height="24px"><path d="M 4.7070312 3.2929688 L 3.2929688 4.7070312 L 10.585938 12 L 3.2929688 19.292969 L 4.7070312 20.707031 L 12 13.414062 L 19.292969 20.707031 L 20.707031 19.292969 L 13.414062 12 L 20.707031 4.7070312 L 19.292969 3.2929688 L 12 10.585938 L 4.7070312 3.2929688 z"/></svg>
            </div>

            {queryRes?.loading &&
                <div className='ds-postlist__loader'>
                    <Spinner />
                </div>
            }

            {queryRes?.posts?.length &&
                <div className='ds-postlist__inner'>
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
                </div>
            }
        </div>
    )
}

export default PostList;
