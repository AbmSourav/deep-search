import { useEffect, useRef, useState } from "react";

import SearchBar from "./SearchBar";
import SearchOptions from "./SearchOptions";
import PostList from "./PostList";

const DeepSearch = ({ props }) => {
    const [focusStatus, setFocusStatus] = useState(false)
    const [queryData, setQueryData] = useState({})
    const [queryRes, setQueryRes] = useState({})
    const [wrapHeight, setWrapHeight] = useState(null)
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

    return (
        <div style={{ height: wrapHeight ? `${wrapHeight}px` : 'auto' }}>
            <div className={classNames}>
                {focusStatus &&
                    (<div className="ds-overlay" onClick={handleCloseDropDown}></div>)
                }

                {Object.keys(queryRes).length === 0 &&
                    <SearchBar
                    props={props}
                    setFocusStatus={setFocusStatus}
                    queryData={queryData}
                    setQueryData={setQueryData}
                    setQueryRes={setQueryRes}
                    elmHeight={wrapRef}
                    />
                }

                {focusStatus && !queryRes?.loading && Object.keys(queryRes).length === 0 &&
                    <SearchOptions
                    props={props}
                    queryData={queryData}
                    setQueryData={setQueryData}
                    />
                }

                {focusStatus && (queryRes?.loading || Object.keys(queryRes).length > 0) &&
                    <PostList
                    props={props}
                    queryRes={queryRes}
                    setQueryRes={setQueryRes}
                    setQueryData={setQueryData}
                    queryData={queryData}
                    />
                }
            </div>
        </div>
    )
}

export default DeepSearch;
