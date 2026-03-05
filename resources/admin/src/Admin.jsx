import { useEffect, useState } from 'react';
import { ToggleControl, Button, Notice, TabPanel, __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

const Admin = () => {
    const [ showPagination, setShowPagination ] = useState(1)
    const [ postPerPage, setPostPerPage ] = useState(5)
    const [ cacheEnabled, setCacheEnabled ] = useState(1)
    const [ cacheTtl, setCacheTtl ] = useState(15)
    const [ isSubmitting, setisSubmitting ] = useState(false)
    const [ isClearingCache, setIsClearingCache ] = useState(false)
    const [ notice, setNotice ] = useState({ show: false, type: '', message: '' });
    const [ hasMinPHP, setHasMinPHP ] = useState(true)

    useEffect(() => {
        if (dsAdmin?.currentPHPVersion < dsAdmin?.minPHPVersion) {
            setHasMinPHP(false)
            setNotice({
                show: true,
                type: 'error',
                message: sprintf(__('Deep Search requires PHP version %s or higher. Please upgrade your PHP version.', 'deep-search'), dsAdmin?.minPHPVersion)
            });
            return;
        }

        const form = new FormData()
        form.append('action', 'getConfigurations');
        form.append('nonce', dsAdmin.nonce);

        fetch(dsAdmin.ajaxUrl, {
            method: 'POST',
            body: form,
        })
        .then((res => res.json()))
        .then(data => {
            const configs = data?.data?.configs
            setPostPerPage(parseInt(configs?.posts_per_page))
            setShowPagination(configs?.show_pagination == true ? 1 : 0)
            setCacheEnabled(configs?.cache_enabled == true ? 1 : 0)
            setCacheTtl(parseInt(configs?.cache_ttl) || 15)
        })
        .catch(error => {
            console.error(error)
        })
    }, [])

    const handleShowPagination = (val) => {
        const paginationView = val == true ? 1 : 0
        setShowPagination(paginationView)
    }

    const handlePostPerPage = (value) => {
        setPostPerPage(value)
    }

    const handleClearCache = () => {
        setIsClearingCache(true)
        const form = new FormData()
        form.append('action', 'dsClearCache');
        form.append('nonce', dsAdmin?.nonce);

        fetch(dsAdmin?.ajaxUrl, {
            method: 'POST',
            body: form,
        })
        .then((res => res.json()))
        .then(() => {
            setNotice({
                show: true,
                type: 'success',
                message: __('Cache cleared', 'deep-search')
            });
        })
        .catch(error => console.error(error))
        .finally(() => setIsClearingCache(false))
    }

    const handleSubmit = () => {
        setisSubmitting(true)

        const form = new FormData()
        form.append('action', 'setConfigurations');
        form.append('nonce', dsAdmin?.nonce);
        form.append('postPerPage', postPerPage)
        form.append('showPagination', showPagination)
        form.append('cacheEnabled', cacheEnabled)
        form.append('cacheTtl', cacheTtl)

        fetch(dsAdmin?.ajaxUrl, {
            method: 'POST',
            body: form,
        })
        .then((res => res.json()))
        .then(data => {
            setNotice({
                show: true,
                type: 'success',
                message: __('Configurations updated', 'deep-search')
            });
        })
        .catch(error => {
            console.error(error)
        })
        .finally(() => setisSubmitting(false))
    }

    if (hasMinPHP === false) {
        return (
            <Notice
            status={notice.type}
            isDismissible={false}
            onRemove={() => setNotice({ show: false, type: '', message: '' })}
            className='mb-4'
            >
                {notice.message}
            </Notice>
        )
    }

    return (
        <>
        {notice.show && (
            <Notice
            status={notice.type}
            isDismissible={true}
            onRemove={() => setNotice({ show: false, type: '', message: '' })}
            className='mb-4'
            >
                {notice.message}
            </Notice>
        )}

        <div className="ds-configs">
            <TabPanel
                tabs={[
                    { name: 'configurations', title: __('Configurations', 'deep-search') },
                    { name: 'cache', title: __('Cache', 'deep-search') },
                ]}
            >
                {(tab) => (
                    <div className="ds-configs__tab-content">
                        {tab.name === 'configurations' && (
                            <>
                                <div className="ds-configs__config">
                                    <div className="ds-configs__config-label">
                                        {__('Show Pagination', 'deep-search')}
                                    </div>
                                    <ToggleControl
                                        label=''
                                        checked={showPagination == 1 ? true : false}
                                        onChange={handleShowPagination}
                                        __nextHasNoMarginBottom={true}
                                    />
                                </div>

                                <div className="ds-configs__config">
                                    <div className="ds-configs__config-label">
                                        {__('Posts per page', 'deep-search')}
                                    </div>
                                    <NumberControl
                                    value={postPerPage}
                                    onChange={handlePostPerPage}
                                    min={1}
                                    max={20}
                                    __next40pxDefaultSize
                                    />
                                </div>

                                <div className="ds-configs__actions">
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
                            </>
                        )}

                        {tab.name === 'cache' && (
                            <>
                                <div className="ds-configs__config">
                                    <div className="ds-configs__config-label">
                                        {__('Enable Cache', 'deep-search')}
                                    </div>
                                    <ToggleControl
                                        label=''
                                        checked={cacheEnabled == 1}
                                        onChange={(val) => setCacheEnabled(val === true ? 1 : 0)}
                                        __nextHasNoMarginBottom={true}
                                    />
                                </div>

                                {cacheEnabled == 1 &&
                                    <div className="ds-configs__config">
                                        <div className="ds-configs__config-label">
                                            {__('Cache Duration (minutes)', 'deep-search')}
                                        </div>
                                        <NumberControl
                                        value={cacheTtl}
                                        onChange={(value) => setCacheTtl(value)}
                                        min={1}
                                        max={1440}
                                        __next40pxDefaultSize
                                        />
                                    </div>
                                }

                                <div className="ds-configs__actions">
                                    <Button
                                    type='button'
                                    disabled={isSubmitting || isClearingCache}
                                    isBusy={isSubmitting}
                                    className='ds-configs__save'
                                    onClick={handleSubmit}
                                    >
                                        {__('Save', 'deep-search')}
                                    </Button>

                                    <Button
                                    type='button'
                                    disabled={isClearingCache || isSubmitting}
                                    isBusy={isClearingCache}
                                    className='ds-configs__clear-cache'
                                    onClick={handleClearCache}
                                    >
                                        {__('Clear Cache', 'deep-search')}
                                    </Button>
                                </div>
                            </>
                        )}
                    </div>
                )}
            </TabPanel>
        </div>
        </>
    )
}

export default Admin;
