import React, { useState, useMemo, useCallback, useRef, useEffect } from 'react';
import PropTypes from 'prop-types';
import { createPortal } from 'react-dom';
import ToolboxTabBtn from './toolbox.tab.button';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';

const DEFAULT_TOOLBOX_MIN_WIDTH = 360;
const DEFAULT_TOOLBOX_MAX_WIDTH = 520;
const TOOLBOX_WIDTH = 'ibexa-pb-toolbox-width';
const toolboxLocalWidth = Number(window.localStorage.getItem(TOOLBOX_WIDTH));

const Toolbox = ({ setSidebarSide, children }) => {
    const infobarTogglerContainer = document.querySelector('.ibexa-pb-action-bar__right');
    const toolboxRef = useRef(null);
    const backdrop = new window.ibexa.core.Backdrop({ isTransparent: true });
    const clientXWhenStartDraging = useRef(0);
    const [isResizing, setIsResizing] = useState(false);
    const [toolboxWidth, setToolboxWidth] = useState(toolboxLocalWidth || DEFAULT_TOOLBOX_MIN_WIDTH);
    const [tab, setTab] = useState({
        activeTab: children[0].type.name,
        title: children[0].props.title,
        isOpened: true,
    });
    const changeActiveTab = (tabName) => {
        setTab((prevTab) => {
            const shouldBeOpened = prevTab.activeTab === tabName || tabName === '' || !prevTab.isOpened;

            return {
                activeTab: prevTab.activeTab === tabName ? '' : tabName,
                isOpened: shouldBeOpened ? !prevTab.isOpened : prevTab.isOpened,
                title: children.find((child) => child.type.name === tabName).props.title,
            };
        });
    };

    const wrapperClasses = createCssClassNames({
        'c-pb-toolbox': true,
        'c-pb-toolbox--collapsed': !tab.isOpened,
        'c-pb-toolbox--resizing': isResizing,
    });
    const renderedChildren = useMemo(() => children.find((child) => tab.activeTab === child.type.name), [tab, children]);
    const toolboxTabBtns = useMemo(
        () => (
            <div className="ibexa-pb-action-bar__action-btns">
                {children.map(({ props: { iconName, name, title }, type: { name: componentTypeName } }) => (
                    <ToolboxTabBtn
                        key={componentTypeName}
                        name={name}
                        title={title}
                        changeActiveTab={changeActiveTab}
                        iconName={iconName}
                        tab={tab}
                        tabName={componentTypeName}
                    />
                ))}
            </div>
        ),
        [children, tab],
    );
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
                const newToolboxWidth = toolboxWidth + (clientXWhenStartDraging.current - clientX);
                const minMaxToolboxWidth = Math.min(Math.max(newToolboxWidth, DEFAULT_TOOLBOX_MIN_WIDTH), DEFAULT_TOOLBOX_MAX_WIDTH);

                window.localStorage.setItem(TOOLBOX_WIDTH, minMaxToolboxWidth);
                setToolboxWidth(minMaxToolboxWidth);
            }
        },
        [isResizing],
    );

    useEffect(() => {
        if (isResizing) {
            document.addEventListener('mousemove', resize, false);
            document.addEventListener('mouseup', stopResizing, false);
            backdrop.show();

            return () => {
                document.removeEventListener('mousemove', resize, false);
                document.removeEventListener('mouseup', stopResizing, false);
                backdrop.remove();
            };
        }
    }, [isResizing, resize, stopResizing]);

    return (
        <>
            {createPortal(toolboxTabBtns, infobarTogglerContainer)}
            <div ref={toolboxRef} style={{ width: tab.isOpened ? toolboxWidth : 0 }} className={wrapperClasses}>
                <div className="c-pb-toolbox__action-bar">
                    <div className="c-pb-toolbox__toggler" onClick={setSidebarSide}>
                        <Icon name="collapse" extraClasses="ibexa-icon--small" />
                    </div>
                </div>
                <div className="c-pb-toolbox__title-bar">
                    <h3 className="c-pb-toolbox__title">{tab.title}</h3>
                </div>
                <div className="c-pb-toolbox__content">{renderedChildren}</div>
                <div className="c-pb-toolbox__resizer" onMouseDown={startResizing} />
            </div>
        </>
    );
};

Toolbox.propTypes = {
    children: PropTypes.node.isRequired,
    setSidebarSide: PropTypes.func.isRequired,
};

Toolbox.defaultProps = {};

export default Toolbox;
