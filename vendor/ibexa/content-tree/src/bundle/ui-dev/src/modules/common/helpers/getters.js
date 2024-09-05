import { getAdminUiConfig } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

export const getContentLink = (item) => {
    const locationHref = window.Routing.generate('ibexa.content.view', {
        contentId: item.contentId,
        locationId: item.locationId,
    });

    return locationHref;
};

export const isUser = (contentTypesInfoMap, { ContentInfo }) => {
    const adminUiConfig = getAdminUiConfig();
    const contentType = contentTypesInfoMap[ContentInfo.Content.ContentType._href];

    return adminUiConfig.userContentTypes.includes(contentType.identifier);
};

export const getPermissions = (permissions = [], permissionName) => {
    return permissions.find(({ _name }) => _name === permissionName);
};
