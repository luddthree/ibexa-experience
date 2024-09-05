import { handleRequestResponse } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/request.helper';
import { getAdminUiConfig, getRestInfo } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { getRequestHeaders, getRequestMode } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/request.helper';

const { location: currentLocation } = window;
const DEFAULT_INSTANCE_URL = currentLocation.origin;

const ENDPOINT_TRASH_FAKE_LOCATION = '/api/ibexa/v2/content/trash';
const ENDPOINT_CONTENT_TYPES = '/api/ibexa/v2/content/types';
const ENDPOINT_LOAD_SUBITEMS = '/api/ibexa/v2/location/tree/load-subitems';
const ENDPOINT_LOAD_SUBTREE = '/api/ibexa/v2/location/tree/load-subtree';
const ENDPOINT_LOAD_LOCATIONS_WITH_PERMISSIONS = '/api/ibexa/v2/module/universal-discovery/locations';
const ENDPOINT_LOCATION = '/api/ibexa/v2/content/locations';
const ENDPOINT_USER = '/api/ibexa/v2/user/users';
const ENDPOINT_CREATE_VIEW = '/api/ibexa/v2/views';
const ENDPOINT_BOOKMARK = '/api/ibexa/v2/bookmark';
const HEADERS_CREATE_VIEW = {
    Accept: 'application/vnd.ibexa.api.View+json; version=1.1',
    'Content-Type': 'application/vnd.ibexa.api.ViewInput+json; version=1.1',
};
const ENDPOINT_BULK = '/api/ibexa/v2/bulk';
const HEADERS_BULK = {
    Accept: 'application/vnd.ibexa.api.BulkOperationResponse+json',
    'Content-Type': 'application/vnd.ibexa.api.BulkOperation+json',
};
const QUERY_LIMIT = 50;
const AGGREGATIONS_LIMIT = 4;

const getItemPath = (item, path) => {
    const hasPreviousPath = path && path.length;
    const itemPath = `${hasPreviousPath ? `${path}/` : ''}${item.locationId}`;

    return itemPath;
};

const mapChildrenToSubitemsDeep = (tree, path) =>
    tree.map((subtree) => {
        subtree.path = getItemPath(subtree, path);

        mapChildrenToSubitems(subtree);
        subtree.subitems = mapChildrenToSubitemsDeep(subtree.subitems, subtree.path);

        return subtree;
    });

const mapChildrenToSubitems = (location) => {
    location.totalSubitemsCount = location.totalChildrenCount;
    location.subitems = location.children;

    delete location.totalChildrenCount;
    delete location.children;
    delete location.displayLimit;

    return location;
};

export const loadContentTypes = (siteaccess, instanceUrl = DEFAULT_INSTANCE_URL) => {
    const path = `${instanceUrl}${ENDPOINT_CONTENT_TYPES}`;
    const request = new Request(path, {
        method: 'GET',
        headers: {
            Accept: 'application/vnd.ibexa.api.ContentTypeInfoList+json',
            'X-Siteaccess': siteaccess,
        },
        mode: getRequestMode({ instanceUrl }),
        credentials: 'same-origin',
    });

    return fetch(request).then(handleRequestResponse);
};

export const findLocationsById = ({
    token,
    siteaccess,
    accessToken,
    id,
    limit = QUERY_LIMIT,
    offset = 0,
    instanceUrl = DEFAULT_INSTANCE_URL,
}) => {
    const path = `${instanceUrl}${ENDPOINT_CREATE_VIEW}`;
    const body = JSON.stringify({
        ViewInput: {
            identifier: `tree-builder-locations-by-id-${id}`,
            public: false,
            LocationQuery: {
                FacetBuilders: {},
                SortClauses: { SectionIdentifier: 'ascending' },
                Filter: { LocationIdCriterion: id },
                limit,
                offset,
            },
        },
    });
    const request = new Request(path, {
        method: 'POST',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: HEADERS_CREATE_VIEW,
        }),
        body,
        mode: getRequestMode({ instanceUrl }),
        credentials: 'include',
    });

    return fetch(request)
        .then(handleRequestResponse)
        .then((response) => response.View.Result.searchHits.searchHit.map((searchHit) => searchHit.value.Location));
};

export const loadLocationsWithPermissions = ({ siteaccess, id, instanceUrl = DEFAULT_INSTANCE_URL }) => {
    const path = `${instanceUrl}${ENDPOINT_LOAD_LOCATIONS_WITH_PERMISSIONS}?locationIds=${id}`;
    const request = new Request(path, {
        method: 'GET',
        mode: getRequestMode({ instanceUrl }),
        credentials: 'include',
        headers: getRequestHeaders({
            siteaccess,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.LocationListData+json',
            },
        }),
    });

    return fetch(request)
        .then(handleRequestResponse)
        .then(({ LocationList }) => LocationList.locations);
};

export const loadSubtree = ({
    token,
    siteaccess,
    accessToken,
    subtree,
    filter,
    sortClause,
    sortOrder,
    instanceUrl = DEFAULT_INSTANCE_URL,
}) => {
    let path = `${instanceUrl}${ENDPOINT_LOAD_SUBTREE}`;

    if (sortClause && sortOrder) {
        path += `?sortClause=${sortClause}&sortOrder=${sortOrder}`;
    }

    const requestBody = {
        LoadSubtreeRequest: {
            '_media-type': 'application/vnd.ibexa.api.ContentTreeLoadSubtreeRequest',
            nodes: subtree,
        },
    };

    if (filter) {
        requestBody.LoadSubtreeRequest.Filter = filter;
    }

    const request = new Request(path, {
        method: 'POST',
        mode: getRequestMode({ instanceUrl }),
        credentials: 'include',
        body: JSON.stringify(requestBody),
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.ContentTreeRoot+json',
                'Content-Type': 'application/vnd.ibexa.api.ContentTreeLoadSubtreeRequest+json',
            },
        }),
    });

    return fetch(request)
        .then(handleRequestResponse)
        .then((data) => {
            const loadedSubtree = data.ContentTreeRoot.ContentTreeNodeList;

            return mapChildrenToSubitemsDeep(loadedSubtree);
        });
};

export const loadLocationItems = ({
    siteaccess,
    accessToken,
    parentLocationId,
    limit = QUERY_LIMIT,
    offset = 0,
    instanceUrl = DEFAULT_INSTANCE_URL,
}) => {
    const path = `${instanceUrl}${ENDPOINT_LOAD_SUBITEMS}/${parentLocationId}/${limit}/${offset}`;
    const request = new Request(path, {
        method: 'GET',
        mode: getRequestMode({ instanceUrl }),
        credentials: 'include',
        headers: getRequestHeaders({
            siteaccess,
            accessToken,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.ContentTreeNode+json',
            },
        }),
    });

    return fetch(request)
        .then(handleRequestResponse)
        .then((data) => {
            const location = data.ContentTreeNode;

            location.children = location.children.map(mapChildrenToSubitems);

            return mapChildrenToSubitems(location);
        });
};

const getBulkDeleteUserRequestOperation = (contentId) => {
    const { instanceUrl } = getRestInfo();

    return {
        uri: `${instanceUrl}${ENDPOINT_USER}/${contentId}`,
        method: 'DELETE',
    };
};

const getBulkMoveRequestOperation = (pathString, destination) => {
    const { instanceUrl } = getRestInfo();

    return {
        uri: `${instanceUrl}${ENDPOINT_LOCATION}${pathString.slice(0, -1)}`,
        method: 'MOVE',
        headers: {
            Destination: destination,
        },
    };
};

const getBulkCopyRequestOperation = (pathString, destination, instanceUrl = DEFAULT_INSTANCE_URL) => ({
    uri: `${instanceUrl}${ENDPOINT_LOCATION}${pathString.slice(0, -1)}`,
    method: 'COPY',
    headers: {
        Destination: destination,
    },
});

export const buildBulkRequest = (requestBodyOperations, { token, siteaccess, accessToken, instanceUrl = DEFAULT_INSTANCE_URL }) => {
    const path = `${instanceUrl}${ENDPOINT_BULK}`;
    const request = new Request(path, {
        method: 'POST',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: HEADERS_BULK,
        }),
        body: JSON.stringify({
            bulkOperations: {
                operations: requestBodyOperations,
            },
        }),
        mode: getRequestMode({ instanceUrl }),
        credentials: 'include',
    });

    return request;
};

const processBulkResponse = (items, { BulkOperationResponse }) => {
    const { operations } = BulkOperationResponse;
    const results = Object.entries(operations).reduce(
        (itemsMatches, [locationId, response]) => {
            const respectiveItem = items.find((item) => item.id === parseInt(locationId, 10));
            const isSuccess = 200 <= response.statusCode && response.statusCode <= 299;

            if (isSuccess) {
                itemsMatches.success.push(respectiveItem);
            } else {
                itemsMatches.fail.push(respectiveItem);
            }

            return itemsMatches;
        },
        { success: [], fail: [] },
    );

    return Promise.resolve(results);
};

export const moveElements = (items, destination, additionalProperties) => {
    const operations = {};

    items.forEach(({ id, pathString }) => {
        operations[id] = getBulkMoveRequestOperation(pathString, `${ENDPOINT_LOCATION}${destination}`);
    });

    const request = buildBulkRequest(operations, additionalProperties);

    return fetch(request).then(handleRequestResponse).then(processBulkResponse.bind(null, items));
};

export const copyElements = (items, destination, additionalProperties) => {
    const operations = {};

    items.forEach(({ id, pathString }) => {
        operations[id] = getBulkCopyRequestOperation(pathString, `${ENDPOINT_LOCATION}${destination}`);
    });

    const request = buildBulkRequest(operations, additionalProperties);

    return fetch(request).then(handleRequestResponse).then(processBulkResponse.bind(null, items));
};

export const deleteElements = (items, additionalProperties) => {
    const adminUiConfig = getAdminUiConfig();
    const { contentTypes } = additionalProperties;
    const operations = {};

    items.forEach((item) => {
        const { id: locationId, pathString, ContentInfo } = item;
        const contentType = contentTypes[ContentInfo.Content.ContentType._href];
        const isUserContentItem = adminUiConfig.userContentTypes.includes(contentType.identifier);

        if (isUserContentItem) {
            const contentId = ContentInfo.Content._id;

            operations[locationId] = getBulkDeleteUserRequestOperation(contentId);
        } else {
            operations[locationId] = getBulkMoveRequestOperation(pathString, ENDPOINT_TRASH_FAKE_LOCATION);
        }
    });

    const request = buildBulkRequest(operations, additionalProperties);

    return fetch(request).then(handleRequestResponse).then(processBulkResponse.bind(null, items));
};

const getBulkBookmarkRequestOperation = (locationId, isAddAction, instanceUrl = DEFAULT_INSTANCE_URL) => {
    const method = isAddAction ? 'POST' : 'DELETE';

    return {
        uri: `${instanceUrl}${ENDPOINT_BOOKMARK}/${locationId}`,
        method,
    };
};

export const addToBookmarks = (items, additionalProperties, instanceUrl = DEFAULT_INSTANCE_URL) => {
    const operations = {};

    items.forEach(({ id, locationId }) => {
        operations[id] = getBulkBookmarkRequestOperation(locationId, true, instanceUrl);
    });

    const request = buildBulkRequest(operations, additionalProperties);

    return fetch(request).then(handleRequestResponse).then(processBulkResponse.bind(null, items));
};

export const removeFromBookmarks = (items, additionalProperties, instanceUrl = DEFAULT_INSTANCE_URL) => {
    const operations = {};

    items.forEach(({ id, locationId }) => {
        operations[id] = getBulkBookmarkRequestOperation(locationId, false, instanceUrl);
    });

    const request = buildBulkRequest(operations, additionalProperties);

    return fetch(request).then(handleRequestResponse).then(processBulkResponse.bind(null, items));
};

export const findSuggestions = (parentLocationId, { siteaccess, token, accessToken, instanceUrl = DEFAULT_INSTANCE_URL }) => {
    const body = JSON.stringify({
        ViewInput: {
            identifier: 'view_with_aggregation',
            LocationQuery: {
                limit: 0,
                offset: 0,
                Filter: {
                    ParentLocationIdCriterion: parentLocationId,
                },
                Aggregations: [
                    {
                        ContentTypeTermAggregation: {
                            name: 'suggestions',
                            limit: AGGREGATIONS_LIMIT,
                        },
                    },
                ],
            },
        },
    });

    const request = new Request(ENDPOINT_CREATE_VIEW, {
        method: 'POST',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: {
                ...HEADERS_CREATE_VIEW,
            },
        }),
        body,
        mode: getRequestMode({ instanceUrl }),
        credentials: 'same-origin',
    });

    return fetch(request)
        .then(handleRequestResponse)
        .then((response) => {
            const suggestions = response.View?.Result.aggregations[0]?.entries.map(
                ({
                    key: {
                        ContentType: { identifier },
                    },
                }) => ({
                    identifier,
                }),
            );

            return suggestions;
        });
};

export const loadLocationItemExtendedInfo = ({ internalItem }) => {
    const { token, siteaccess, accessToken, instanceUrl } = getRestInfo();
    const extendedInfoPath = `${instanceUrl}/api/ibexa/v2/location/tree/${internalItem.locationId}/extended-info`;
    const request = new Request(extendedInfoPath, {
        method: 'GET',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.ContentTreeExtendedNodeInfo+json',
                'Content-Type': 'application/vnd.ibexa.api.ContentTreeLoadNodeExtendedInfoRequest+json',
            },
        }),
        mode: getRequestMode({ instanceUrl }),
        credentials: 'include',
    });

    return fetch(request)
        .then(handleRequestResponse)
        .then(({ ContentTreeNodeExtendedInfo }) => ContentTreeNodeExtendedInfo);
};
