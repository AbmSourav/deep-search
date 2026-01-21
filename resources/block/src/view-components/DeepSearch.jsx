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
            <SearchBar
            props={props}
            setFocusStatus={setFocusStatus}
            queryData={queryData}
            setQueryRes={setQueryRes}
            />
            <SearchOptions
            props={props}
            focusStatus={focusStatus}
            queryData={queryData}
            setQueryData={setQueryData}
            />

            <PostList
            queryRes={queryRes}
            />
        </div>
    )
}

export default DeepSearch;
