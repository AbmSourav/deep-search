import { useEffect, useRef, useState } from 'react'

import SearchBar from './SearchBar'
import SearchOptions from './SearchOptions'
import PostList from './PostList'

const DeepSearch = ({ props }) => {
    const [focusStatus, setFocusStatus] = useState(false)
    const [queryData, setQueryData] = useState({})
    const [queryRes, setQueryRes] = useState({})
    const [wrapHeight, setWrapHeight] = useState(null)
    const [isRestDisabled, setIsRestDisabled] = useState(false)
    const wrapRef = useRef(null)

    const classNames = focusStatus ? 'ds-wrap active' : 'ds-wrap'

    useEffect(() => {
        if (focusStatus && wrapRef.current) {
            setWrapHeight(wrapRef.current.offsetHeight)
        } else {
            setWrapHeight(null)
        }
    }, [focusStatus])

    const handleCloseDropDown = () => {
        setFocusStatus(false)
        setQueryRes({})
    }

    const commonStyles = {
        backgroundColor: props?.attibutes?.backgroundColor || '#fff',
    }

    return (
        <div style={{ height: wrapHeight ? `${wrapHeight}px` : 'auto' }}>
            <div className={classNames} style={commonStyles}>
                {focusStatus && (
                    <div
                        className="ds-overlay"
                        onClick={handleCloseDropDown}
                    ></div>
                )}

                {Object.keys(queryRes).length === 0 && (
                    <SearchBar
                        props={props}
                        setFocusStatus={setFocusStatus}
                        queryState={{ queryData, setQueryData }}
                        setQueryRes={setQueryRes}
                        elmHeight={wrapRef}
                        rest={{ isRestDisabled, setIsRestDisabled }}
                    />
                )}
                {focusStatus &&
                    !queryRes?.loading &&
                    Object.keys(queryRes).length === 0 && (
                        <SearchOptions
                            props={props}
                            queryState={{ queryData, setQueryData }}
                        />
                    )}

                {focusStatus &&
                    (queryRes?.loading || Object.keys(queryRes).length > 0) && (
                        <PostList
                            props={props}
                            resData={{ queryRes, setQueryRes }}
                            queryState={{ queryData, setQueryData }}
                            rest={{ isRestDisabled, setIsRestDisabled }}
                        />
                    )}
            </div>
        </div>
    )
}

export default DeepSearch
