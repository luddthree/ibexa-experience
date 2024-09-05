import React, { useEffect, useState, useCallback, useRef } from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

export const CONFIG_PANEL_TYPE_LEFT = 'CONFIG_PANEL_TYPE_LEFT';
export const CONFIG_PANEL_TYPE_RIGHT = 'CONFIG_PANEL_TYPE_RIGHT';
export const LEFT_PANEL_TYPES = {
    FIELDS: 'fields',
    LAYOUT_SELECTOR: 'layout-selector',
    SCHEDULER: 'scheduler',
    VISIBILITY: 'visibility',
};
const DEFAULT_CONFIG_PANEL_MIN_WIDTH = 830;
const DEFAULT_CONFIG_PANEL_MAX_WIDTH = 1920;
const LEFT_CONFIG_PANEL_WIDTH_KEY_NAME = 'ibexa-pb-config-panel-width-left';
const RIGHT_CONFIG_PANEL_WIDTH_KEY_NAME = 'ibexa-pb-config-panel-width-right';

const ConfigPanel = ({ extraClasses, type, children, title, subtitle, isClosed, isDistractionFreeModeActive, leftPanelType }) => {
    const isLeftPanel = type === CONFIG_PANEL_TYPE_LEFT;
    const isRightPanel = type === CONFIG_PANEL_TYPE_RIGHT;
    const localStoragePanelWidthName = isLeftPanel ? LEFT_CONFIG_PANEL_WIDTH_KEY_NAME : RIGHT_CONFIG_PANEL_WIDTH_KEY_NAME;
    const localStoragePanelWidth = window.localStorage.getItem(localStoragePanelWidthName);
    const configPanelLocalWidth = localStoragePanelWidth ? Number(localStoragePanelWidth) : DEFAULT_CONFIG_PANEL_MIN_WIDTH;
    const [isResizing, setIsResizing] = useState(false);
    const [configPanelWidth, setConfigPanelWidth] = useState(configPanelLocalWidth);
    const configPanelRef = useRef(null);
    const clientXWhenStartDraging = useRef(0);
    const backdrop = new window.ibexa.core.Backdrop({ extraClasses: ['ibexa-pb-config-panel__backdrop'] });
    const className = createCssClassNames({
        'ibexa-pb-config-panel': true,
        'ibexa-pb-config-panel--closed': isClosed,
        'ibexa-pb-config-panel--left': isLeftPanel && !isDistractionFreeModeActive,
        'ibexa-pb-config-panel--right': isRightPanel && !isDistractionFreeModeActive,
        'ibexa-pb-config-panel--resizing': isResizing,
        'ibexa-pb-config-panel--distraction-free-mode-active': isDistractionFreeModeActive,
        [`ibexa-pb-config-panel--${leftPanelType}`]: isLeftPanel,
        [extraClasses]: true,
    });
    const resizerClassName = createCssClassNames({
        'ibexa-pb-config-panel__resizer': true,
        'ibexa-pb-config-panel__resizer--left': isLeftPanel,
        'ibexa-pb-config-panel__resizer--right': isRightPanel,
    });
    const startResizing = useCallback(
        ({ clientX }) => {
            clientXWhenStartDraging.current = clientX;
            setIsResizing(true);
        },
        [clientXWhenStartDraging],
    );
    const stopResizing = useCallback(() => {
        setIsResizing(false);
    }, []);
    const resize = useCallback(
        ({ clientX }) => {
            if (isResizing) {
                const newConfigPanelWidth = isLeftPanel
                    ? configPanelWidth - (clientXWhenStartDraging.current - clientX)
                    : configPanelWidth + (clientXWhenStartDraging.current - clientX);
                const maxConfigPanelWidth = Math.min(window.screen.width, DEFAULT_CONFIG_PANEL_MAX_WIDTH);
                const minMaxConfigPanelWidth = Math.min(Math.max(newConfigPanelWidth, DEFAULT_CONFIG_PANEL_MIN_WIDTH), maxConfigPanelWidth);

                window.localStorage.setItem(localStoragePanelWidthName, minMaxConfigPanelWidth);
                setConfigPanelWidth(minMaxConfigPanelWidth);
            }
        },
        [isResizing],
    );
    const toggleBtnIndex = (isOpened) => {
        if (isRightPanel) {
            return;
        }

        const toggleBtn = document.querySelector(`.ibexa-pb-action-bar__action-btn--${leftPanelType}`);

        toggleBtn.style.zIndex = isOpened ? 656 : 0;
    };

    useEffect(() => {
        document.dispatchEvent(new CustomEvent('ibexa-pb-config-panel-added'));
    }, []);

    useEffect(() => {
        if (!isClosed) {
            backdrop.show();
            toggleBtnIndex(true);
            setConfigPanelWidth(configPanelLocalWidth);
        }

        return () => {
            backdrop.remove();
            toggleBtnIndex(false);
        };
    }, [isClosed]);

    useEffect(() => {
        if (isResizing) {
            document.addEventListener('mousemove', resize, false);
            document.addEventListener('mouseup', stopResizing, false);

            return () => {
                document.removeEventListener('mousemove', resize, false);
                document.removeEventListener('mouseup', stopResizing, false);
            };
        }
    }, [isResizing, resize, stopResizing]);

    return (
        <>
            <div
                ref={configPanelRef}
                style={{ ...(!isDistractionFreeModeActive && { width: isClosed ? 0 : configPanelWidth }) }}
                className={className}
            >
                <div className="ibexa-pb-config-panel__header">
                    <h2 className="ibexa-pb-config-panel__title">{title}</h2>
                    {subtitle && <h4 className="ibexa-pb-config-panel__subtitle">{subtitle}</h4>}
                </div>
                {children}
                <div className={resizerClassName} onMouseDown={startResizing} />
            </div>
        </>
    );
};

ConfigPanel.propTypes = {
    extraClasses: PropTypes.string,
    type: PropTypes.string,
    children: PropTypes.node.isRequired,
    title: PropTypes.string,
    subtitle: PropTypes.string,
    onCancel: PropTypes.func.isRequired,
    isClosed: PropTypes.bool,
    isDistractionFreeModeActive: PropTypes.bool,
    leftPanelType: PropTypes.string,
};

ConfigPanel.defaultProps = {
    extraClasses: '',
    type: CONFIG_PANEL_TYPE_LEFT,
    title: null,
    subtitle: null,
    isClosed: false,
    isDistractionFreeModeActive: false,
    leftPanelType: null,
};

export default ConfigPanel;
