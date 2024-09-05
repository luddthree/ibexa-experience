import React, { useContext, useState, useEffect, useRef, forwardRef, useImperativeHandle } from 'react';
import PropTypes from 'prop-types';

import { getAdminUiConfig, getRootDOMElement } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import Pagination from '@ibexa-admin-ui-modules/common/pagination/pagination';
import { useSearchByQueryFetch } from '@ibexa-admin-ui-modules/universal-discovery/hooks/useSearchByQueryFetch';
import {
    AllowedContentTypesContext,
    ContainersOnlyContext,
    ContentTypesMapContext,
    MultipleConfigContext,
    SelectedLocationsContext,
    SortingContext,
    SortOrderContext,
} from '@ibexa-admin-ui-modules/universal-discovery/universal.discovery.module';
import PaginationInfo from '@ibexa-admin-ui-modules/common/pagination/pagination.info';
import Icon from '@ibexa-admin-ui-modules/common/icon/icon';
import { fileSizeToString } from '@ibexa-admin-ui-modules/common/helpers/text.helper';

import ItemsViewTopBar from './items.view.top.bar';
import Grid from '../grid-view/grid.view';
import List from '../list-view/list.view';
import ItemsViewNoItems from './items.view.no.items';
import { FiltersContext, SearchAggregationsContext } from '../../image.picker.tab.module';
import { getImageFormatFromMimeType } from '../../helpers/mime.type.helper';
import {
    FILTERS_ACTION_FILTERS_CLEAR,
    FILTERS_DATA_CREATED_FROM,
    FILTERS_DATA_CREATED_TO,
    FILTERS_DATA_HEIGHT_MAX,
    FILTERS_DATA_HEIGHT_MIN,
    FILTERS_DATA_ORIENTATIONS,
    FILTERS_DATA_LANGUAGES,
    FILTERS_DATA_MIME_TYPES,
    FILTERS_DATA_SEARCH_TEXT,
    FILTERS_DATA_SIZE_MAX,
    FILTERS_DATA_SIZE_MIN,
    FILTERS_DATA_WIDTH_MAX,
    FILTERS_DATA_WIDTH_MIN,
    FILTERS_DATA_TAGS,
} from '../../hooks/useFilters';
import { useDebounce } from '../../hooks/useDebounce';

export const GRID_VIEW = 'GRID_VIEW';
export const LIST_VIEW = 'LIST_VIEW';

const ItemsView = forwardRef(({ itemsPerPage }, ref) => {
    const { damWidget: damWidgetConfig } = getAdminUiConfig();
    const { image: imagePickerConfig } = damWidgetConfig;
    const rootDOMElement = getRootDOMElement();
    const [selectedLocations, dispatchSelectedLocationsAction] = useContext(SelectedLocationsContext);
    const [multiple] = useContext(MultipleConfigContext);
    const { filters, setLastAppliedFilters, selectedLocationData, dispatchFiltersAction } = useContext(FiltersContext);
    const { setSearchAggregations } = useContext(SearchAggregationsContext);
    const [appliedFilters, setAppliedFilters] = useState(filters);
    const lastSearchAbortControllerRef = useRef(null);
    const searchText = filters[FILTERS_DATA_SEARCH_TEXT];
    const searchTextDebounced = useDebounce(searchText);
    const containersOnly = useContext(ContainersOnlyContext);
    const allowedContentTypes = useContext(AllowedContentTypesContext);
    const [sorting] = useContext(SortingContext);
    const [sortOrder] = useContext(SortOrderContext);
    const checkIsSelectable = (location) => {
        const contentType = contentTypesMap[location.ContentInfo.Content.ContentType._href];
        const { isContainer, identifier } = contentType;
        const isAllowedContentType = allowedContentTypes?.includes(identifier) ?? true;

        return (!containersOnly || isContainer) && isAllowedContentType;
    };
    const checkIsSelected = (location) => selectedLocations.some((selectedLocation) => selectedLocation.location.id === location.id);
    const toggleSelectedLocation = (location) => {
        if (!checkIsSelectable(location)) {
            return;
        }

        const isSelected = checkIsSelected(location);

        if (isSelected) {
            dispatchSelectedLocationsAction({ type: 'REMOVE_SELECTED_LOCATION', id: location.id });
        } else {
            if (!multiple) {
                dispatchSelectedLocationsAction({ type: 'CLEAR_SELECTED_LOCATIONS' });
            }

            dispatchSelectedLocationsAction({ type: 'ADD_SELECTED_LOCATION', location });
        }
    };

    const contentTypesMap = useContext(ContentTypesMapContext);
    const [offset, setOffset] = useState(0);
    const [activeView, setActiveView] = useState(GRID_VIEW);
    const prevAppliedFilters = useRef(null);
    const [isSearching, data, searchByQuery] = useSearchByQueryFetch();
    const isLoading = isSearching || data.items === undefined;
    const getImageCriterionDataFromFilters = (filtersToApply) => {
        const imageCriterionData = {};
        const selectedMimeTypesCount = filtersToApply[FILTERS_DATA_MIME_TYPES]?.length ?? 0;

        if (selectedMimeTypesCount !== 0) {
            imageCriterionData.mimeTypes = filtersToApply[FILTERS_DATA_MIME_TYPES];
        }

        if (filtersToApply[FILTERS_DATA_SIZE_MIN]) {
            imageCriterionData.size ??= {};
            imageCriterionData.size.min = filtersToApply[FILTERS_DATA_SIZE_MIN];
        }

        if (filtersToApply[FILTERS_DATA_SIZE_MAX]) {
            imageCriterionData.size ??= {};
            imageCriterionData.size.max = filtersToApply[FILTERS_DATA_SIZE_MAX];
        }

        if (filtersToApply[FILTERS_DATA_ORIENTATIONS]?.length ?? 0 !== 0) {
            imageCriterionData.orientation = filtersToApply[FILTERS_DATA_ORIENTATIONS];
        }

        if (filtersToApply[FILTERS_DATA_WIDTH_MIN]) {
            imageCriterionData.width = imageCriterionData.width ?? {};
            imageCriterionData.width.min = filtersToApply[FILTERS_DATA_WIDTH_MIN];
        }

        if (filtersToApply[FILTERS_DATA_WIDTH_MAX]) {
            imageCriterionData.width = imageCriterionData.width ?? {};
            imageCriterionData.width.max = filtersToApply[FILTERS_DATA_WIDTH_MAX];
        }

        if (filtersToApply[FILTERS_DATA_HEIGHT_MIN]) {
            imageCriterionData.height = imageCriterionData.height ?? {};
            imageCriterionData.height.min = filtersToApply[FILTERS_DATA_HEIGHT_MIN];
        }

        if (filtersToApply[FILTERS_DATA_HEIGHT_MAX]) {
            imageCriterionData.height = imageCriterionData.height ?? {};
            imageCriterionData.height.max = filtersToApply[FILTERS_DATA_HEIGHT_MAX];
        }

        return imageCriterionData;
    };
    const getContentNameCriterion = (text) => {
        return text ? `*${text}*` : '';
    };
    const getFullTextCriterion = (tags) => {
        let fullTextCriterion = '';

        if (tags && tags.length !== 0) {
            fullTextCriterion = tags.join(' OR ');
        }

        return fullTextCriterion;
    };
    const getImageFiltersFromFilters = (filtersToApply) => {
        if (filtersToApply[FILTERS_DATA_CREATED_FROM] && filtersToApply[FILTERS_DATA_CREATED_TO]) {
            return {
                DateMetadataCriterion: {
                    Target: 'created',
                    Value: [filtersToApply[FILTERS_DATA_CREATED_FROM], filtersToApply[FILTERS_DATA_CREATED_TO]],
                    Operator: 'between',
                },
            };
        }

        if (filtersToApply[FILTERS_DATA_CREATED_FROM]) {
            return {
                DateMetadataCriterion: {
                    Target: 'created',
                    Value: filtersToApply[FILTERS_DATA_CREATED_FROM],
                    Operator: 'gte',
                },
            };
        } else if (filtersToApply[FILTERS_DATA_CREATED_TO]) {
            return {
                DateMetadataCriterion: {
                    Target: 'created',
                    Value: filtersToApply[FILTERS_DATA_CREATED_TO],
                    Operator: 'lte',
                },
            };
        }

        return null;
    };
    const search = () => {
        const shouldResetOffset = JSON.stringify(prevAppliedFilters.current) !== JSON.stringify(appliedFilters) && offset !== 0;

        prevAppliedFilters.current = appliedFilters;

        if (shouldResetOffset) {
            setOffset(0);

            return;
        }

        const subtreePathString = selectedLocationData.path;
        const languageCode = appliedFilters[FILTERS_DATA_LANGUAGES] ?? null;
        const imageCriterionData = getImageCriterionDataFromFilters(appliedFilters);
        const { contentTypeIdentifiers } = imagePickerConfig;
        const { KeywordTermAggregation } = imagePickerConfig.aggregations ?? {};
        const imageFilters = getImageFiltersFromFilters(appliedFilters);
        const tags = appliedFilters[FILTERS_DATA_TAGS];
        const contentNameCriterion = getContentNameCriterion(searchTextDebounced);
        const fullTextCriterion = getFullTextCriterion(tags);
        const aggregations = [];

        if (KeywordTermAggregation) {
            aggregations.push({ KeywordTermAggregation });
        }

        setLastAppliedFilters(appliedFilters);

        if (lastSearchAbortControllerRef.current) {
            lastSearchAbortControllerRef.current.abort();
        }

        const { abortController } = searchByQuery(
            null,
            contentTypeIdentifiers,
            null,
            subtreePathString,
            itemsPerPage,
            offset,
            languageCode,
            sorting,
            sortOrder,
            imageCriterionData,
            aggregations,
            imageFilters,
            fullTextCriterion,
            contentNameCriterion,
        );

        lastSearchAbortControllerRef.current = abortController;
    };
    const performSearch = () => {
        setAppliedFilters(filters);
    };
    const resetOffset = () => {
        setOffset(0);
    };
    const refresh = () => {
        const areFiltersEmpty = Object.values(filters).every((filterValue) => !filterValue);

        if (areFiltersEmpty) {
            search();

            return;
        }

        dispatchFiltersAction({ type: FILTERS_ACTION_FILTERS_CLEAR });
    };

    useEffect(() => {
        rootDOMElement.addEventListener('ibexa-content-items-view-refresh', refresh, false);

        return () => {
            rootDOMElement.removeEventListener('ibexa-content-items-view-refresh', refresh, false);
        };
    }, [searchTextDebounced, appliedFilters, offset, selectedLocationData.path]);

    useEffect(() => {
        if (!selectedLocationData.path) {
            return;
        }

        search();
    }, [searchTextDebounced, appliedFilters, offset, selectedLocationData.path, sorting, sortOrder]);

    const getEzimageField = (location) => {
        const imageField = location.ContentInfo.Content.CurrentVersion.Version.Fields.field.find(
            (field) => field.fieldTypeIdentifier === 'ezimage',
        );

        return imageField;
    };
    const getImageFileSize = (location) => {
        const imageField = getEzimageField(location);

        if (!imageField?.fieldValue) {
            return null;
        }

        return fileSizeToString(imageField.fieldValue.fileSize);
    };
    const getImageFormat = (location) => {
        const imageField = getEzimageField(location);

        if (!imageField?.fieldValue) {
            return null;
        }

        return getImageFormatFromMimeType(imageField.fieldValue.mime).toUpperCase();
    };
    const getImageHeight = (location) => {
        const imageField = getEzimageField(location);

        if (!imageField?.fieldValue) {
            return null;
        }

        return imageField.fieldValue.height;
    };
    const getImageWidth = (location) => {
        const imageField = getEzimageField(location);

        if (!imageField?.fieldValue) {
            return null;
        }

        return imageField.fieldValue.width;
    };
    const getGridItems = () =>
        data.items.map((location) => ({
            itemId: location.id,
            thumbnail: location.ContentInfo.Content.CurrentVersion.Version.Thumbnail,
            iconPath: contentTypesMap[location.ContentInfo.Content.ContentType._href].thumbnail,
            title: location.ContentInfo.Content.TranslatedName,
            detailA: getImageFormat(location),
            detailB: getImageFileSize(location),
            isSelected: checkIsSelected(location),
            onClick: () => toggleSelectedLocation(location),
        }));
    const getListItems = () =>
        data.items.map((location) => ({
            id: location.id,
            name: location.ContentInfo.Content.TranslatedName,
            fileFormat: getImageFormat(location),
            fileSize: getImageFileSize(location),
            imageHeight: getImageHeight(location),
            imageWidth: getImageWidth(location),
            thumbnail: location.ContentInfo.Content.CurrentVersion.Version.Thumbnail,
            iconPath: contentTypesMap[location.ContentInfo.Content.ContentType._href].thumbnail,
            isSelected: checkIsSelected(location),
            onClick: () => toggleSelectedLocation(location),
        }));
    const changePage = (pageIndex) => {
        setOffset(pageIndex * itemsPerPage);
    };

    useImperativeHandle(
        ref,
        () => ({
            performSearch,
            resetOffset,
        }),
        [performSearch, resetOffset],
    );

    useEffect(() => {
        setSearchAggregations(data.aggregations);
    }, [data]);

    return (
        <div className="c-ip-items-view">
            <ItemsViewTopBar activeView={activeView} onViewChange={setActiveView} />
            <div className="c-ip-items-view__container">
                {activeView === GRID_VIEW && !isLoading && <Grid items={getGridItems()} />}
                {activeView === LIST_VIEW && !isLoading && <List items={getListItems()} />}
                {!isLoading && data.items.length === 0 && <ItemsViewNoItems />}
                {isLoading && (
                    <div className="c-ip-items-view__loading-spinner">
                        <Icon name="spinner" extraClasses="ibexa-icon--medium ibexa-spin" />
                    </div>
                )}
                <div className="c-ip-items-view__pagination">
                    {!isLoading && (
                        <>
                            <PaginationInfo totalCount={data.count} viewingCount={data.items.length} />
                            <Pagination
                                proximity={1}
                                itemsPerPage={itemsPerPage}
                                activePageIndex={offset / itemsPerPage}
                                totalCount={data.count}
                                onPageChange={changePage}
                                disabled={false}
                            />
                        </>
                    )}
                </div>
            </div>
        </div>
    );
});

ItemsView.propTypes = {
    itemsPerPage: PropTypes.number,
};

ItemsView.defaultProps = {
    itemsPerPage: 10,
};

ItemsView.displayName = 'ItemsView';

export default ItemsView;
