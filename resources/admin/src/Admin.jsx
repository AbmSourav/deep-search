import { useEffect, useState } from 'react';
import { ToggleControl, Button, __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const Admin = () => {
    const [ showPagination, setShowPagination ] = useState(true)
    const [ postPerPage, setPostPerPage ] = useState(5)
    const [ isSubmitting, setisSubmitting ] = useState(false)

    useEffect(() => {
        const form = new FormData()
        form.append('action', 'getConfigurations');
        form.append('nonce', dsAdmin.nonce);

        fetch(dsAdmin.ajaxUrl, {
            method: 'POST',
            body: form,
        })
        .then((res => res.json()))
        .then(data => {
            setPostPerPage(parseInt(data?.data?.configs?.posts_per_page))
            setShowPagination(data?.data?.configs?.show_pagination)
        })
        .catch(error => {
            console.error(error)
        })
    }, [])

    const handleShowPagination = () => {
        setShowPagination(!showPagination)
    }

    const handlePostPerPage = (value) => {
        console.log('ppp', value)
        setPostPerPage(value)
    }

    const handleSubmit = () => {
        setisSubmitting(true)
        const configs = {postPerPage, showPagination}
        console.log('button submit', configs)

        const form = new FormData()
        form.append('action', 'setConfigurations');
        form.append('nonce', dsAdmin.nonce);
        form.append('configs', JSON.stringify(configs));

        fetch(dsAdmin.ajaxUrl, {
            method: 'POST',
            body: form,
        })
        .then((res => res.json()))
        .then(data => {
            console.log('res', data)
        })
        .catch(error => {
            console.error(error)
        })
        .finally(() => setisSubmitting(false))
    }

    return (
        <div className="ds-configs">
            <h2>{__('Configurations', 'deep-search')}</h2>

            <div className="ds-configs__config">
                <div className="ds-configs__config-label">
                    {__('Show Pagination', 'deep-search')}
                </div>
                <ToggleControl
                    label=''
                    checked={showPagination}
                    onChange={handleShowPagination}
                />
            </div>

            <div className="ds-configs__config">
                <div className="ds-configs__config-label">
                    {__('Post per page', 'deep-search')}
                </div>
                <NumberControl
                value={postPerPage}
                onChange={handlePostPerPage}
                min={-1}
                max={10}
                />
            </div>

            <Button
            type='button'
            disabled={isSubmitting}
            isBusy={isSubmitting}
            className='ds-configs__save'
            onClick={handleSubmit}
            >
                {__('Save', 'deep-search')}
            </Button>
        </div>
    )
}

export default Admin;
