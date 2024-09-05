import React, { createContext, useContext, useState, useRef } from 'react';
import PropTypes from 'prop-types';

import { BuildItemContext } from '../../tree.builder.module';

export const PlaceholderContext = createContext();
export const DRAG_ID = 'DRAG';
const SIBLING_POSITION_PREV = 'prev';
const SIBLING_POSITION_NEXT = 'next';

const PlaceholderProvider = ({ children }) => {
    const buildItem = useContext(BuildItemContext);
    const [activeItemsData, setActiveItemsData] = useState([]);
    const [placeholderData, setPlaceholderData] = useState({});
    const placeholderDataRef = useRef(null);
    const getInsertIndexAndParent = ({ event, index, isExpanded, parent, item }) => {
        const { currentTarget, clientY } = event;
        const { top, height } = currentTarget.getBoundingClientRect();
        const relativeMousePosition = clientY - top;
        let output = {};

        if (parent?.isContainer) {
            if (item.isContainer) {
                if (relativeMousePosition < height / 4) {
                    output = {
                        nextIndex: index,
                        nextParent: parent,
                        sibling: item,
                        siblingPosition: SIBLING_POSITION_PREV,
                    };
                } else if (relativeMousePosition < height - height / 4) {
                    if (isExpanded) {
                        output = {
                            nextIndex: 0,
                            nextParent: item,
                            sibling: buildItem(item.subitems[0]),
                            siblingPosition: SIBLING_POSITION_PREV,
                        };
                    } else {
                        output = {
                            nextIndex: -1,
                            nextParent: item,
                        };
                    }
                } else {
                    if (isExpanded) {
                        output = {
                            nextIndex: 0,
                            nextParent: item,
                            sibling: buildItem(item.subitems[0]),
                            siblingPosition: SIBLING_POSITION_PREV,
                        };
                    } else {
                        output = {
                            nextIndex: index + 1,
                            nextParent: parent,
                            sibling: item,
                            siblingPosition: SIBLING_POSITION_NEXT,
                        };
                    }
                }
            } else {
                if (relativeMousePosition < height / 2) {
                    output = {
                        nextIndex: index,
                        nextParent: parent,
                        sibling: item,
                        siblingPosition: SIBLING_POSITION_PREV,
                    };
                } else {
                    output = {
                        nextIndex: index + 1,
                        nextParent: parent,
                        sibling: item,
                        siblingPosition: SIBLING_POSITION_NEXT,
                    };
                }
            }
        } else {
            if (item.isContainer) {
                if (isExpanded) {
                    output = {
                        nextIndex: 0,
                        nextParent: item,
                        sibling: buildItem(item.subitems[0]),
                        siblingPosition: SIBLING_POSITION_PREV,
                    };
                } else {
                    output = {
                        nextIndex: -1,
                        nextParent: item,
                    };
                }
            }
        }

        return output;
    };
    const mouseMove = (event, { item, parent, index, isExpanded }) => {
        const isMouseOverActiveItem = activeItemsData.some((activeItem) => activeItem.id === item.id);

        if (isMouseOverActiveItem || activeItemsData.length === 0) {
            return;
        }

        const nextState = getInsertIndexAndParent({ event, index, item, parent, isExpanded });

        setPlaceholderData(nextState);
        placeholderDataRef.current = nextState;
    };
    const clearPlaceholder = () => {
        placeholderDataRef.current = null;
        setActiveItemsData([]);
        setPlaceholderData({});
    };

    return (
        <PlaceholderContext.Provider
            value={{
                setActiveItemsData,
                mouseMove,
                placeholderData,
                setPlaceholderData,
                placeholderDataRef,
                clearPlaceholder,
            }}
        >
            {children}
        </PlaceholderContext.Provider>
    );
};

PlaceholderProvider.propTypes = {
    children: PropTypes.node.isRequired,
};

export default PlaceholderProvider;
