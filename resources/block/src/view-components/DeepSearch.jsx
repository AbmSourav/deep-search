import { useState } from "react";

import SearchBar from "./SearchBar";
import SearchOptions from "./SearchOptions";
import PostList from "./PostList";

const DeepSearch = ({ props }) => {
    const [focusStatus, setFocusStatus] = useState(false)
    const [queryData, setQueryData] = useState({})
    const [queryRes, setQueryRes] = useState({})

    const classNames = focusStatus ? 'ds-wrap active' : 'ds-wrap'

    return (
        <div className={classNames}>
            {console.log('props', props)}
            {focusStatus &&
                (<div className="ds-overlay" onClick={() => setFocusStatus(false)}></div>)
            }

            {Object.keys(queryRes).length === 0 &&
                <SearchBar
                props={props}
                setFocusStatus={setFocusStatus}
                queryData={queryData}
                setQueryRes={setQueryRes}
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
                queryRes={queryRes}
                setQueryRes={setQueryRes}
                setQueryData={setQueryData}
                />
            }
        </div>
    )
}

export default DeepSearch;
