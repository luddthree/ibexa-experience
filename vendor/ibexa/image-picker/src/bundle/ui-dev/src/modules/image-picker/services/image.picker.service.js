import {
    getRestInfo,
    getAdminUiConfig,
    getTranslator,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import {
    getContentTypeData,
    createContentTypeDataMapByHref,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/content.type.helper';
import { showErrorNotification } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/notification.helper';
import {
    getRequestHeaders,
    getRequestMode,
    getJsonFromResponse,
    getStatusFromResponse,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/request.helper';
import {
    findLocationsById,
    moveElements as baseMoveElements,
    deleteElements as baseDeleteElements,
} from '@ibexa-content-tree/src/bundle/ui-dev/src/modules/common/services/content.tree.service';

const CONTENT_ENDPOINT = '/api/ibexa/v2/content/objects';

export const getContentNameFieldIdentifierForCreate = async (contentTypeIdentifier) => {
    let contentTypeData = {};
    const Translator = getTranslator();
    const { siteaccess, token, accessToken, instanceUrl } = getRestInfo();
    const adminUiConfig = getAdminUiConfig();
    const contentType = getContentTypeData(contentTypeIdentifier);
    const getContentTypeDataRequest = new Request(`${instanceUrl}${contentType.href}`, {
        method: 'GET',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.ContentType+json',
            },
        }),
        mode: getRequestMode({ instanceUrl }),
        credentials: 'same-origin',
    });

    try {
        const response = await fetch(getContentTypeDataRequest);

        contentTypeData = await getJsonFromResponse(response);
    } catch (errro) {
        showErrorNotification(
            Translator.trans(
                /*@Desc("An error occurred while fetching Content Type. Try again later")*/ 'quick_actions.validation_msgs.fetch_content_type_error',
                {},
                'ibexa_image_picker',
            ),
        );
    }

    if (!contentTypeData) {
        showErrorNotification(
            Translator.trans(
                /*@Desc("Can’t find content type. Try again later.")*/ 'quick_actions.validation_msgs.find_content_type_error',
                {},
                'ibexa_image_picker',
            ),
        );

        return;
    }

    const { nameSchemaIdentifiers } = adminUiConfig.damWidget.folder;
    const contentTypeFieldDefinitions = contentTypeData.ContentType.FieldDefinitions.FieldDefinition;
    const nameFieldIdentifier = nameSchemaIdentifiers.find((nameSchemaIdentifier) =>
        contentTypeFieldDefinitions.find(
            (contentTypeField) => contentTypeField.isRequired && contentTypeField.identifier === nameSchemaIdentifier,
        ),
    );

    return nameFieldIdentifier;
};

export const getContentNameFieldIdentifierForEdit = async (locationId) => {
    let locationData = [];
    const Translator = getTranslator();
    const restInfo = getRestInfo();
    const adminUiConfig = getAdminUiConfig();

    try {
        locationData = await findLocationsById({
            ...restInfo,
            id: locationId,
        });
    } catch (error) {
        showErrorNotification(
            Translator.trans(
                /*@Desc("An error occurred while fetching location data. Try again later")*/ 'quick_actions.validation_msgs.fetch_location_error',
                {},
                'ibexa_image_picker',
            ),
        );
    }

    if (!locationData.length) {
        showErrorNotification(
            Translator.trans(
                /*@Desc("Can’t find locations.")*/ 'quick_actions.validation_msgs.find_locations_error',
                {},
                'ibexa_image_picker',
            ),
        );

        return;
    }

    const contentFields = locationData[0].ContentInfo.Content.CurrentVersion.Version.Fields.field;
    const { nameSchemaIdentifiers } = adminUiConfig.damWidget.folder;
    const nameFieldIdentifier = nameSchemaIdentifiers.find((nameSchemaIdentifier) =>
        contentFields.find((contentField) => contentField.fieldValue && contentField.fieldDefinitionIdentifier === nameSchemaIdentifier),
    );

    return nameFieldIdentifier;
};

export const createDraft = async (
    { contentTypeIdentifier, parentLocationHref, nameFieldIdentifier, languageCode, createdName },
    actionErrorCallback,
) => {
    const { siteaccess, token, accessToken, instanceUrl } = getRestInfo();
    const contentType = getContentTypeData(contentTypeIdentifier);
    const createDraftEndpoint = `${instanceUrl}${CONTENT_ENDPOINT}`;
    const createDraftRequest = new Request(createDraftEndpoint, {
        method: 'POST',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.Content+json',
                'Content-Type': 'application/vnd.ibexa.api.ContentCreate+json',
            },
        }),
        mode: getRequestMode({ instanceUrl }),
        credentials: 'same-origin',
        body: JSON.stringify({
            ContentCreate: {
                mainLanguageCode: languageCode,
                ContentType: { _href: contentType.href },
                LocationCreate: {
                    ParentLocation: { _href: parentLocationHref },
                    sortField: 'PATH',
                    sortOrder: 'ASC',
                },
                Section: null,
                alwaysAvailable: true,
                remoteId: null,
                fields: {
                    field: [
                        {
                            fieldDefinitionIdentifier: nameFieldIdentifier,
                            languageCode: languageCode,
                            fieldTypeIdentifier: 'ezstring',
                            fieldValue: createdName,
                        },
                    ],
                },
            },
        }),
    });

    try {
        const response = await fetch(createDraftRequest);
        const parsedResponse = await getJsonFromResponse(response);

        return parsedResponse;
    } catch ({ message }) {
        actionErrorCallback ? actionErrorCallback() : showErrorNotification(message);
    }
};

export const createDraftFromCurrentVersion = async ({ contentId }, actionErrorCallback) => {
    const { siteaccess, token, accessToken, instanceUrl } = getRestInfo();
    const copyEndpointUrl = `${instanceUrl}${CONTENT_ENDPOINT}/${contentId}/currentversion`;
    const createDraftRequest = new Request(copyEndpointUrl, {
        method: 'COPY',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.Version+json',
            },
        }),
        mode: getRequestMode({ instanceUrl }),
        credentials: 'same-origin',
    });

    try {
        const response = await fetch(createDraftRequest);
        const parsedResponse = await getJsonFromResponse(response);

        return parsedResponse;
    } catch ({ message }) {
        actionErrorCallback ? actionErrorCallback() : showErrorNotification(message);
    }
};

export const updateDraft = async ({ versionHref, nameFieldIdentifier, languageCode, updatedName }, actionErrorCallback) => {
    const { siteaccess, token, accessToken, instanceUrl } = getRestInfo();
    const updateEndpointUrl = `${instanceUrl}${versionHref}`;
    const updateDraftRequest = new Request(updateEndpointUrl, {
        method: 'PATCH',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.Version+json',
                'Content-Type': 'application/vnd.ibexa.api.VersionUpdate+json',
            },
        }),
        mode: getRequestMode({ instanceUrl }),
        credentials: 'same-origin',
        body: JSON.stringify({
            VersionUpdate: {
                fields: {
                    field: [
                        {
                            fieldDefinitionIdentifier: nameFieldIdentifier,
                            languageCode: languageCode,
                            fieldTypeIdentifier: 'ezstring',
                            fieldValue: updatedName,
                        },
                    ],
                },
            },
        }),
    });

    try {
        const response = await fetch(updateDraftRequest);
        const parsedResponse = await getJsonFromResponse(response);

        return parsedResponse;
    } catch ({ message }) {
        actionErrorCallback ? actionErrorCallback() : showErrorNotification(message);
    }
};

export const publishDraft = async ({ versionHref }, actionErrorCallback) => {
    const { siteaccess, token, accessToken, instanceUrl } = getRestInfo();
    const publishDraftRequest = new Request(`${instanceUrl}${versionHref}`, {
        method: 'POST',
        headers: getRequestHeaders({
            token,
            siteaccess,
            accessToken,
            extraHeaders: {
                Accept: 'application/vnd.ibexa.api.ContentInfo+json',
                'X-HTTP-Method-Override': 'PUBLISH',
            },
        }),
        mode: getRequestMode({ instanceUrl }),
        credentials: 'same-origin',
    });

    try {
        const response = await fetch(publishDraftRequest);

        return getStatusFromResponse(response);
    } catch ({ message }) {
        actionErrorCallback ? actionErrorCallback() : showErrorNotification(message);
    }
};

export const deleteElements = async ({ selectedData, subitemsLoadLimit }, actionErrorCallback) => {
    const restInfo = getRestInfo();
    const entryIds = selectedData.map((item) => item.id);
    const sourceIds = entryIds.join(',');
    const contentTypesInfoMap = createContentTypeDataMapByHref();

    try {
        const elements = await findLocationsById({ ...restInfo, limit: subitemsLoadLimit, id: sourceIds });
        const deletedItemsResponse = await baseDeleteElements(elements, { ...restInfo, contentTypes: contentTypesInfoMap });

        return deletedItemsResponse;
    } catch ({ message }) {
        actionErrorCallback ? actionErrorCallback() : showErrorNotification(message);
    }
};

export const moveElements = async ({ selectedData, destination, subitemsLoadLimit }, actionErrorCallback) => {
    const restInfo = getRestInfo();
    const entryIds = selectedData.map((item) => item.id);
    const sourceIds = entryIds.join(',');

    try {
        const elements = await findLocationsById({ ...restInfo, limit: subitemsLoadLimit, id: sourceIds });
        const movingElementsResponse = await baseMoveElements(elements, destination, { ...restInfo });

        return movingElementsResponse;
    } catch ({ message }) {
        actionErrorCallback ? actionErrorCallback() : showErrorNotification(message);
    }
};
