import {
    handleRequestResponse,
    handleRequestResponseStatus,
} from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/request.helper';

const NO_CONTENT_STATUS_CODE = 204;
const ENDPOINT_BULK = '/api/ibexa/v2/bulk';
const HEADERS_BULK = {
    Accept: 'application/vnd.ibexa.api.BulkOperationResponse+json',
    'Content-Type': 'application/vnd.ibexa.api.BulkOperation+json',
};

const cleanEntryIds = (entryIds) => entryIds.filter((el) => !!el);

// TODO: should be moved to admin-ui helpers
const handleRequestResponseWrapper = (response) => {
    const statusCode = handleRequestResponseStatus(response);

    if (statusCode === NO_CONTENT_STATUS_CODE) {
        return null;
    }

    return handleRequestResponse(response);
};

const handleAbortException = (error) => {
    if (error?.name === 'AbortError') {
        return;
    }

    throw error;
};

export const loadTreeRoot = ({ taxonomyName }) => {
    const loadTreeRootUrl = window.Routing.generate('ibexa.taxonomy.tree.root', { taxonomyName }, true);
    const request = new Request(loadTreeRootUrl, {
        method: 'GET',
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request).then(handleRequestResponseWrapper);
};
export const loadTree = ({ taxonomyName, entryIds }) => {
    const loadTreeUrl = window.Routing.generate('ibexa.taxonomy.tree.subtree', { taxonomyName, entryIds: cleanEntryIds(entryIds) }, true);
    const request = new Request(loadTreeUrl, {
        method: 'GET',
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request).then(handleRequestResponseWrapper);
};

export const loadNode = ({ taxonomyName, entryId }) => {
    const loadNodeUrl = window.Routing.generate('ibexa.taxonomy.tree.node', { taxonomyName, entryId }, true);
    const request = new Request(loadNodeUrl, {
        method: 'GET',
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request).then(handleRequestResponseWrapper);
};

export const moveElements = (entries, { restInfo, taxonomyName }) => {
    const route = `/api/ibexa/v2/taxonomy/${taxonomyName}/entries/move`;
    const request = new Request(route, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/vnd.ibexa.api.TaxonomyEntryBulkMove+json',
            'X-CSRF-Token': restInfo.token,
            'X-Siteaccess': restInfo.siteaccess,
        },
        body: JSON.stringify({
            TaxonomyEntryBulkMove: {
                entries,
            },
        }),
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request).then(handleRequestResponseWrapper);
};

export const deleteElements = (entries, { restInfo, taxonomyName }) => {
    const route = `/api/ibexa/v2/taxonomy/${taxonomyName}/entries`;
    const request = new Request(route, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/vnd.ibexa.api.TaxonomyEntryBulkRemove+json',
            'X-CSRF-Token': restInfo.token,
            'X-Siteaccess': restInfo.siteaccess,
        },
        body: JSON.stringify({
            TaxonomyEntryBulkRemove: {
                entries,
            },
        }),
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request).then(handleRequestResponseWrapper);
};

const getBulkAssignContent = (taxonomyName, contentId, entriesIds) => ({
    uri: `/api/ibexa/v2/taxonomy/${taxonomyName}/entry-assignments/assign-to-content`,
    content: JSON.stringify({
        TaxonomyEntryAssignToContent: {
            content: contentId,
            entries: entriesIds,
        },
    }),
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/vnd.ibexa.api.TaxonomyEntryAssignToContent+json',
    },
    method: 'POST',
});

export const assignContent = (contentIds, entriesIds, { restInfo, taxonomyName }) => {
    const requestBodyOperations = contentIds.map((contentId) => getBulkAssignContent(taxonomyName, contentId, entriesIds));

    const request = new Request(ENDPOINT_BULK, {
        method: 'POST',
        headers: {
            ...HEADERS_BULK,
            'X-CSRF-Token': restInfo.token,
            'X-Siteaccess': restInfo.siteaccess,
        },
        body: JSON.stringify({
            bulkOperations: {
                operations: requestBodyOperations,
            },
        }),
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request).then(handleRequestResponseWrapper);
};

export const loadSearchResults = ({ taxonomyName, searchValue, languageCode, signal }) => {
    const loadTreeUrl = window.Routing.generate('ibexa.taxonomy.tree.search', { taxonomyName, query: searchValue, languageCode }, true);
    const request = new Request(loadTreeUrl, {
        method: 'GET',
        mode: 'same-origin',
        credentials: 'same-origin',
    });

    return fetch(request, { signal }).then(handleRequestResponseWrapper).catch(handleAbortException);
};
