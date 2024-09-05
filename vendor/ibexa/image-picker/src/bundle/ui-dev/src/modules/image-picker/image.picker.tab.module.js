import React, { createContext, useContext, useEffect, useState, useRef } from 'react';

import {
    RestInfoContext,
    RootLocationIdContext,
    MarkedLocationIdContext,
    DropdownPortalRefContext,
} from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/universal.discovery.module';
import {
    findLocationsById,
    loadLocationsWithPermissions,
} from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/universal-discovery/services/universal.discovery.service';

import {
    getTranslator,
    SYSTEM_ROOT_LOCATION_ID,
    SYSTEM_ROOT_LOCATION_PATH,
    SYSTEM_ROOT_LOCATION,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { getIconPath } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/icon.helper';
import { getContentTypeDataByHref } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/content.type.helper';

import Snackbar from './components/snackbar/snackbar';
import ItemsView from './components/items-view/items.view';
import Filters from './components/filters/filters';

import TabHeader from './components/tab-header/tab.header';
import TreeBrowserSidebar from './components/tree-browser/tree.browser.sidebar';
import { useFilters } from './hooks/useFilters';

export const rootLocationFakeName = () => {
    const Translator = getTranslator();

    return Translator.trans(/*@Desc("Location")*/ 'root_location.fake_name', {}, 'ibexa_image_picker');
};
export const rootLocationFakeContentTypeIdentifier = 'folder';
export const SelectedItemsContext = createContext();
export const FiltersContext = createContext();
export const SearchAggregationsContext = createContext();
export const RootLocationContext = createContext();
export const PermissionsContext = createContext();

const ImagePickerTabModule = () => {
    const abortControllerRef = useRef();
    const rootLocationId = useContext(RootLocationIdContext);
    const [markedLocationId] = useContext(MarkedLocationIdContext);
    const restInfo = useContext(RestInfoContext);
    const isSystemRootLocation = rootLocationId === SYSTEM_ROOT_LOCATION_ID;
    const dropdownListRef = useContext(DropdownPortalRefContext);
    const [rootLocation, setRootLocation] = useState(isSystemRootLocation ? SYSTEM_ROOT_LOCATION : null);
    const [rootLocationPermissions, setRootLocationPermissions] = useState(null);
    const [lastAppliedFilters, setLastAppliedFilters] = useState(null);
    const [selectedLocationData, setSelectedLocationData] = useState({});
    const [searchAggregations, setSearchAggregations] = useState(null);
    const [selectedLocationPermissions, setSelectedLocationPermissions] = useState(null);
    const { filters, filtersErrors, dispatchFiltersAction } = useFilters();
    const itemsViewRef = useRef();
    const performSearch = () => {
        itemsViewRef.current?.performSearch();
    };

    useEffect(() => {
        if (isSystemRootLocation) {
            return;
        }

        findLocationsById({ ...restInfo, id: rootLocationId, limit: 1 }, ([rootLocationData]) => {
            setRootLocation(rootLocationData);
        });
    }, [rootLocationId, isSystemRootLocation]);

    useEffect(() => {
        const searchLocationId = markedLocationId ?? rootLocationId;

        if (searchLocationId === SYSTEM_ROOT_LOCATION_ID) {
            setSelectedLocationData({
                path: SYSTEM_ROOT_LOCATION_PATH,
                locationId: SYSTEM_ROOT_LOCATION_ID,
                name: rootLocationFakeName(),
                contentTypeIdentifier: rootLocationFakeContentTypeIdentifier,
            });

            return;
        }

        findLocationsById({ ...restInfo, id: markedLocationId, limit: 1 }, ([markedLocationData]) => {
            const contentTypeData = getContentTypeDataByHref(markedLocationData.ContentInfo.Content.ContentType._href);

            setSelectedLocationData({
                path: markedLocationData.pathString,
                locationId: searchLocationId,
                name: markedLocationData.ContentInfo.Content.TranslatedName,
                contentTypeIdentifier: contentTypeData.identifier,
            });
        });
    }, [rootLocationId, markedLocationId]);

    useEffect(() => {
        const { locationId } = selectedLocationData;

        if (!locationId) {
            return;
        }

        abortControllerRef.current?.abort();
        abortControllerRef.current = new AbortController();
        itemsViewRef.current?.resetOffset();

        loadLocationsWithPermissions(
            {
                ...restInfo,
                locationIds: locationId,
                signal: abortControllerRef.current.signal,
            },
            (response) => {
                const permissions = response?.LocationList?.locations?.[0].permissions ?? null;

                if (locationId === rootLocationId) {
                    setRootLocationPermissions(permissions);
                }

                setSelectedLocationPermissions(permissions);
            },
        );
    }, [selectedLocationData]);

    return (
        <RootLocationContext.Provider value={{ rootLocation, setRootLocation }}>
            <PermissionsContext.Provider value={{ rootLocationPermissions, selectedLocationPermissions }}>
                <FiltersContext.Provider
                    value={{
                        performSearch,
                        filters,
                        filtersErrors,
                        dispatchFiltersAction,
                        lastAppliedFilters,
                        setLastAppliedFilters,
                        selectedLocationData,
                        setSelectedLocationData,
                    }}
                >
                    <SearchAggregationsContext.Provider value={{ searchAggregations, setSearchAggregations }}>
                        <div className="m-ip">
                            <div className="c-ip-main-container">
                                <div className="c-ip-main-container__top-bar">
                                    <TabHeader />
                                </div>
                                <div className="c-ip-main-container__content">
                                    <div className="c-ip-main-container__left-sidebar">
                                        <TreeBrowserSidebar />
                                    </div>
                                    <div className="c-ip-main-container__main">
                                        <ItemsView ref={itemsViewRef} />
                                    </div>
                                    <div className="c-ip-main-container__right-sidebar">
                                        <Filters />
                                    </div>
                                </div>
                                <div className="c-ip-select-snackbar" />
                                <div className="c-udw-tab__dropdown-portal" ref={dropdownListRef} />
                            </div>
                            <Snackbar />
                        </div>
                    </SearchAggregationsContext.Provider>
                </FiltersContext.Provider>
            </PermissionsContext.Provider>
        </RootLocationContext.Provider>
    );
};

export const ImagePickerTab = {
    id: 'image_picker',
    priority: 20,
    component: ImagePickerTabModule,
    getLabel: () => {
        const Translator = getTranslator();

        return Translator.trans(/*@Desc("Image Picker")*/ 'image_picker.module.label', {}, 'ibexa_image_picker');
    },
    getIcon: () => getIconPath('bookmark'),
    isHiddenOnList: true,
};

export default ImagePickerTabModule;
