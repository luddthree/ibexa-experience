import React, { useContext } from 'react';

import Icon from '@ibexa-admin-ui-modules/common/icon/icon';

import SidebarHeader from '../sidebar-header/sidebar.header';
import TreeBrowser from './tree.browser';
import { PermissionsContext, RootLocationContext } from '../../image.picker.tab.module';

import {
    getAdminUiConfig,
    getTranslator,
    getRootDOMElement,
} from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { parse as parseTooltip } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/tooltips.helper';
import { getContentTypeName } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/content.type.helper';

const TreeBrowserSidebar = () => {
    const { damWidget } = getAdminUiConfig();
    const Translator = getTranslator();
    const rootDOMElement = getRootDOMElement();
    const { rootLocation } = useContext(RootLocationContext);
    const { rootLocationPermissions } = useContext(PermissionsContext);
    const treeBrowserTitle = Translator.trans(/*@Desc("Folders")*/ 'tree_browser.title', {}, 'ibexa_image_picker');
    const triggerCreateAction = () => {
        rootDOMElement.dispatchEvent(
            new CustomEvent('ibexa-tb-trigger-quick-action-create', {
                detail: {
                    itemId: rootLocation.id,
                },
            }),
        );
    };
    const renderCreateBtn = () => {
        const rootLocationName = rootLocation?.ContentInfo?.Content?.Name;

        if (!rootLocationName || !rootLocationPermissions || !rootLocationPermissions?.create?.hasAccess) {
            return;
        }

        const { contentTypeIdentifier } = damWidget.folder;
        const contentTypeName = getContentTypeName(contentTypeIdentifier);
        const createBtnTitle = Translator.trans(
            /*@Desc("Create %contentTypeName% inside %rootLocationName%")*/ 'tree_browser.create_btn.title',
            {
                contentTypeName,
                rootLocationName,
            },
            'ibexa_image_picker',
        );

        return (
            <button
                ref={(node) => parseTooltip(node)}
                type="button"
                className="btn ibexa-btn ibexa-btn--small ibexa-btn--small ibexa-btn--no-text ibexa-btn--tertiary c-ip-sidebar-header__create-btn"
                onClick={triggerCreateAction}
                title={createBtnTitle}
                data-tooltip-container-selector=".c-ip-sidebar-header"
            >
                <Icon name="create" extraClasses="ibexa-icon--tiny-small" />
            </button>
        );
    };

    return (
        <>
            <SidebarHeader>
                <Icon name="content-tree" extraClasses="ibexa-icon--small" />
                {treeBrowserTitle}
                {renderCreateBtn()}
            </SidebarHeader>
            <TreeBrowser />
        </>
    );
};

export default TreeBrowserSidebar;
