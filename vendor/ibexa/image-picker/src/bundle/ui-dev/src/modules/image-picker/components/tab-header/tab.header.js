import React, { useContext, useMemo } from 'react';

import Icon from '@ibexa-admin-ui-modules/common/icon/icon';
import Search from '@ibexa-admin-ui-modules/common/input/filter.search';
import MultiFileUploadModule, { UDW_TRIGGER_ID } from '@ibexa-admin-ui-modules/multi-file-upload/multi.file.upload.module';
import { CancelContext } from '@ibexa-admin-ui-modules/universal-discovery/universal.discovery.module';
import {
    getTranslator,
    getAdminUiConfig,
    getRootDOMElement,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

import { FiltersContext, PermissionsContext } from '../../image.picker.tab.module';
import { FILTERS_ACTION_FILTER_SET, FILTERS_DATA_SEARCH_TEXT } from '../../hooks/useFilters';

const TabHeader = () => {
    const Translator = getTranslator();
    const adminUiConfig = getAdminUiConfig();
    const rootDOMElement = getRootDOMElement();
    const cancelUDW = useContext(CancelContext);
    const { filters, dispatchFiltersAction, selectedLocationData } = useContext(FiltersContext);
    const { selectedLocationPermissions } = useContext(PermissionsContext);
    const searchText = filters[FILTERS_DATA_SEARCH_TEXT];
    const title = Translator.trans(/*@Desc("Image library")*/ 'filters.thumbnail', {}, 'ibexa_image_picker');
    const handleSearchTextChange = (event) => {
        dispatchFiltersAction({ type: FILTERS_ACTION_FILTER_SET, filterName: FILTERS_DATA_SEARCH_TEXT, value: event.target.value });
    };
    const handleSearchTextClear = () => {
        dispatchFiltersAction({ type: FILTERS_ACTION_FILTER_SET, filterName: FILTERS_DATA_SEARCH_TEXT, value: '' });
    };
    const contentCreatePermissionsConfig = useMemo(() => {
        return adminUiConfig.damWidget.image.contentTypeIdentifiers.reduce(
            (premissions, contentTypeIdentifier) => ({ ...premissions, [contentTypeIdentifier]: true }),
            {},
        );
    }, []);
    const parentInfo = {
        contentTypeIdentifier: selectedLocationData?.contentTypeIdentifier,
        locationPath: selectedLocationData?.path,
        name: selectedLocationData?.name,
        language: selectedLocationData?.language ?? adminUiConfig.languages.priority[0],
    };
    const closePopup = (uploadedFiles) => {
        const wereAnyFilesUploaded = !!uploadedFiles.length;

        if (wereAnyFilesUploaded) {
            rootDOMElement.dispatchEvent(new CustomEvent('ibexa-content-tree-refresh'));
            rootDOMElement.dispatchEvent(new CustomEvent('ibexa-content-items-view-refresh'));
        }
    };
    const mfuContentTypesMap = Object.values(adminUiConfig.contentTypes).reduce((contentTypeDataMap, contentTypeGroup) => {
        for (const contentTypeData of contentTypeGroup) {
            contentTypeDataMap[contentTypeData.href] = contentTypeData;
        }

        return contentTypeDataMap;
    }, {});

    return (
        <div className="c-ip-top-bar">
            <div className="c-ip-top-bar__col c-ip-top-bar__col--left">
                <div className="c-ip-top-bar__title" data-tooltip-container-selector=".m-ip" title={title}>
                    {title}
                </div>
                {selectedLocationPermissions?.create.hasAccess && (
                    <MultiFileUploadModule
                        triggerId={UDW_TRIGGER_ID}
                        adminUiConfig={adminUiConfig}
                        parentInfo={parentInfo}
                        currentLanguage={selectedLocationData?.language ?? adminUiConfig.languages.priority[0]}
                        contentCreatePermissionsConfig={contentCreatePermissionsConfig}
                        onPopupClose={closePopup}
                        onPopupConfirm={closePopup}
                        contentTypesMap={mfuContentTypesMap}
                    />
                )}
            </div>
            <div className="c-ip-top-bar__col c-ip-top-bar__col--center">
                <div className="ibexa-input-text-wrapper ibexa-input-text-wrapper--search ibexa-input-text-wrapper--type-text">
                    <Search onChange={handleSearchTextChange} value={searchText} />
                    <div className="ibexa-input-text-wrapper__actions">
                        <button
                            type="button"
                            className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--clear"
                            tabIndex="-1"
                            onClick={handleSearchTextClear}
                        >
                            <Icon name="discard" extraClasses="ibexa-icon--tiny" />
                        </button>
                        <button
                            type="button"
                            className="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--search"
                            tabIndex="-1"
                        >
                            <Icon name="search" extraClasses="ibexa-icon--small" />
                        </button>
                    </div>
                </div>
            </div>
            <div className="c-ip-top-bar__col c-ip-top-bar__col--right">
                {cancelUDW && (
                    <button
                        className="c-ip-top-bar__cancel-btn btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
                        type="button"
                        onClick={cancelUDW}
                        data-tooltip-container-selector=".c-top-menu__cancel-btn-wrapper"
                    >
                        <Icon name="discard" extraClasses="ibexa-icon--medium" />
                    </button>
                )}
            </div>
        </div>
    );
};

export default TabHeader;
