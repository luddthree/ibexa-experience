import React, { createContext, useState, useEffect, useRef, useContext } from 'react';
import PropTypes from 'prop-types';

import { getRootDOMElement } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import { setCookie } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/cookies.helper';
import useLocalStorage from '../../hooks/useLocalStorage';
import { ResizableContext } from '../../tree.builder.module';

const CLASS_IS_TREE_RESIZING = 'ibexa-is-tree-resizing';
const CONTAINER_CONFIG_ID = 'width-container-state';
const MIN_CONTAINER_WIDTH = 200;
const NARROW_CONTAINER_WIDTH = MIN_CONTAINER_WIDTH + 24;
const FULL_WIDTH_PADDING = 5;
const DEFAULT_CONTAINER_WIDTH = 350;

export const WidthContainerContext = createContext();
export const TreeFullWidthContext = createContext();
export const checkIsTreeCollapsed = (width) => width <= MIN_CONTAINER_WIDTH;
export const isContainerNarrow = (width) => width <= NARROW_CONTAINER_WIDTH;

const WidthContainer = ({ children, moduleId, userId, scrollWrapperRef, isLoaded, useTheme }) => {
    const rootDOMElement = getRootDOMElement();
    const containerRef = useRef();
    const resizeRef = useRef();
    const { isResizable } = useContext(ResizableContext);
    const { getLocalStorageData, saveLocalStorageData } = useLocalStorage(CONTAINER_CONFIG_ID, 'common');
    const saveWidth = (value) => {
        const cookieName = `ibexa-tb_${moduleId}_${userId}_container-width`;

        setCookie(cookieName, value);
    };
    const saveConfig = (id, value) => {
        const data = getLocalStorageData() ?? {};

        data[id] = value;

        saveLocalStorageData(data);
    };

    const getConfig = (id) => {
        const data = getLocalStorageData() ?? {};

        return data[id];
    };
    const [containerData, setContainerData] = useState({ containerWidth: getConfig('width') || DEFAULT_CONTAINER_WIDTH });
    const [treeFullWidth, setTreeFullWidth] = useState(0);
    const { isResizing, containerWidth, resizedContainerWidth } = containerData;
    const width = isResizing ? resizedContainerWidth : containerWidth;
    const prevContainerWidthRef = useRef(width);
    const containerClassName = createCssClassNames({
        'c-tb-width-container c-tb-element': true,
        'c-tb-element--use-theme': useTheme,
        'c-tb-width-container--narrow': isContainerNarrow(width),
        'c-tb-width-container--collapsed': checkIsTreeCollapsed(width),
    });
    const containerAttrs = { className: containerClassName, ref: containerRef };

    const clearDocumentResizingListeners = () => {
        rootDOMElement.removeEventListener('mousemove', changeContainerWidth);
        rootDOMElement.removeEventListener('mouseup', handleResizeEnd);
        rootDOMElement.classList.remove(CLASS_IS_TREE_RESIZING);
    };
    const handleResizeEnd = () => {
        clearDocumentResizingListeners();

        setContainerData((prevState) => {
            if (prevContainerWidthRef.current !== prevState.resizedContainerWidth) {
                setTreeFullWidth(0);
            }

            prevContainerWidthRef.current = prevState.resizedContainerWidth;

            return {
                resizeStartPositionX: 0,
                containerWidth: prevState.resizedContainerWidth,
                isResizing: false,
            };
        });
    };
    const changeContainerWidth = ({ clientX }) => {
        const currentPositionX = clientX;

        setContainerData((prevState) => ({
            ...prevState,
            resizedContainerWidth: prevState.containerWidth + (currentPositionX - prevState.resizeStartPositionX),
        }));
    };
    const addWidthChangeListener = ({ nativeEvent }) => {
        const resizeStartPositionX = nativeEvent.clientX;
        const currentContainerWidth = containerRef.current.getBoundingClientRect().width;

        rootDOMElement.addEventListener('mousemove', changeContainerWidth, false);
        rootDOMElement.addEventListener('mouseup', handleResizeEnd, false);
        rootDOMElement.classList.add(CLASS_IS_TREE_RESIZING);

        setContainerData({
            resizeStartPositionX,
            resizedContainerWidth: currentContainerWidth,
            containerWidth: currentContainerWidth,
            isResizing: true,
        });
    };
    const saveTreeFullWidth = (widthDiff) => {
        setTreeFullWidth((prevState) => Math.max(prevState, widthDiff));
    };
    const expandTreeToFullWidth = () => {
        if (treeFullWidth > 0) {
            setContainerData((prevState) => ({
                ...prevState,
                containerWidth: prevState.containerWidth + treeFullWidth + FULL_WIDTH_PADDING,
            }));
            setTreeFullWidth(0);
        }
    };
    const resizableWrapperClassName = createCssClassNames({
        'c-tb-width-container__resizable-wrapper': true,
        'c-tb-width-container__resizable-wrapper--active': isResizable,
    });

    useEffect(() => {
        saveConfig('width', containerWidth);
        saveWidth(containerWidth);

        rootDOMElement.dispatchEvent(
            new CustomEvent('ibexa-content-resized', {
                detail: {
                    id: moduleId,
                },
            }),
        );
    }, [containerWidth]);

    useEffect(() => {
        let scrollTimeout;
        const scrollListener = (event) => {
            window.clearTimeout(scrollTimeout);

            scrollTimeout = window.setTimeout(
                (scrollTop) => {
                    saveConfig('scrollTop', scrollTop);
                },
                50,
                event.currentTarget.scrollTop,
            );
        };

        scrollWrapperRef.current?.addEventListener('scroll', scrollListener, false);

        return () => {
            clearDocumentResizingListeners();
            scrollWrapperRef.current?.removeEventListener('scroll', scrollListener, false);
        };
    }, [scrollWrapperRef.current, isLoaded]);

    useEffect(() => {
        rootDOMElement.dispatchEvent(
            new CustomEvent('ibexa-tb-rendered', {
                detail: {
                    id: moduleId,
                },
            }),
        );
    }, []);

    if (width && isResizable) {
        containerAttrs.style = { width: `${width}px` };
    }

    return (
        <WidthContainerContext.Provider value={[containerData, setContainerData]}>
            <TreeFullWidthContext.Provider value={saveTreeFullWidth}>
                <div {...containerAttrs}>
                    <div className={resizableWrapperClassName} ref={resizeRef}>
                        {children}
                    </div>
                    {isResizable && (
                        <div
                            className="c-tb-width-container__resize-handler"
                            onMouseDown={addWidthChangeListener}
                            onDoubleClick={expandTreeToFullWidth}
                        />
                    )}
                </div>
            </TreeFullWidthContext.Provider>
        </WidthContainerContext.Provider>
    );
};

WidthContainer.propTypes = {
    children: PropTypes.node.isRequired,
    moduleId: PropTypes.string.isRequired,
    userId: PropTypes.number.isRequired,
    isResizable: PropTypes.bool,
    scrollWrapperRef: PropTypes.shape({ current: PropTypes.instanceOf(Element) }),
    isLoaded: PropTypes.bool,
    useTheme: PropTypes.bool,
};

WidthContainer.defaultProps = {
    isResizable: true,
    scrollWrapperRef: { current: null },
    isLoaded: false,
    useTheme: false,
};

export default WidthContainer;
