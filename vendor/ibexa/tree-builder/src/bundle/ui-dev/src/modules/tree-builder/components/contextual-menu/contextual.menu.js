import React, { useState, useEffect, useRef, useCallback, createContext } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import ActionList from '../action-list/action.list';
import Portal from '../portal/portal';

export const ContextualMenuContext = createContext();

const MENU_DIRECTION = {
    VERTICAL: {
        TOP: 'top',
        BOTTOM: 'bottom',
    },
    HORIZONTAL: {
        LEFT: 'left',
        RIGHT: 'right',
    },
};

const ContextualMenu = ({ item, isDisabled, parent, isExpanded, setIsExpanded, scrollWrapperRef }) => {
    const menuRef = useRef();
    const isClosableRef = useRef();
    const portalRef = useRef(null);
    const menuPortalRef = useRef(null);
    const [isClosable, setIsClosable] = useState(true);
    const [menuDirection, setMenuDirection] = useState({
        horizontal: null,
        vertical: null,
    });
    const [isItemCovered, setIsItemCovered] = useState(false);
    const toggleMenu = useCallback(
        (event) => {
            const isCustomClose = event.target.dataset.customClose === '1';

            if (isDisabled || !isClosableRef.current || isCustomClose) {
                return;
            }

            setIsExpanded((prevState) => !prevState);
        },
        [isDisabled, isClosableRef, setIsExpanded],
    );
    const toggleMenuOnClickOutside = useCallback(
        (event) => {
            if (menuRef.current.contains(event.target)) {
                return;
            }

            toggleMenu(event);
        },
        [menuRef, toggleMenu],
    );
    const wrapperClassName = createCssClassNames({
        'c-tb-contextual-menu': true,
        'c-tb-contextual-menu--burger': true,
        'c-tb-contextual-menu--expanded': isExpanded,
    });
    const iconClassName = createCssClassNames({
        'ibexa-icon--small': true,
        'ibexa-icon--primary': !isDisabled && isExpanded,
    });
    const togglerClassName = createCssClassNames({
        'c-tb-contextual-menu__toggler': true,
        'c-tb-contextual-menu__toggler--disabled': isDisabled,
    });
    const actionListClassName = createCssClassNames({
        'c-tb-contextual-menu': true,
        'c-tb-contextual-menu--portal': true,
    });
    const portalClassName = createCssClassNames({
        'c-tb-portal--hidden': !menuDirection.vertical && !menuDirection.horizontal,
        'c-tb-portal--top': menuDirection.vertical === MENU_DIRECTION.VERTICAL.TOP,
        'c-tb-portal--left': menuDirection.horizontal === MENU_DIRECTION.HORIZONTAL.LEFT,
    });
    const menuContextData = {
        setIsExpanded,
        setIsClosable,
    };
    const updateElementOverflow = () => {
        const itemElement = menuRef.current.closest('.c-tb-list-item-single__element');
        const { top: scrollTop, bottom: scrollBottom } = scrollWrapperRef.current.getBoundingClientRect();
        const { top: itemTop, bottom: itemBottom } = itemElement.getBoundingClientRect();
        const topGap = 10;
        const bottomGap = 15;

        if (scrollTop > itemBottom - topGap || scrollBottom < itemTop + bottomGap) {
            setIsItemCovered(true);
        } else if (scrollTop < itemBottom - topGap || scrollBottom > itemTop + bottomGap) {
            setIsItemCovered(false);
        }
    };
    const updateVerticalPosition = () => {
        if (menuPortalRef.current) {
            const itemElement = parent === 'SINGLE_ITEM' ? menuRef.current.closest('.c-tb-list-item-single__element') : menuRef.current;
            const { height: menuPortalHeight } = menuPortalRef.current.getBoundingClientRect();
            const { y: itemYPosition } = itemElement.getBoundingClientRect();

            if (itemYPosition + menuPortalHeight > window.innerHeight) {
                setMenuDirection((prevPosition) => ({
                    ...prevPosition,
                    vertical: MENU_DIRECTION.VERTICAL.TOP,
                }));
            } else {
                setMenuDirection((prevPosition) => ({
                    ...prevPosition,
                    vertical: MENU_DIRECTION.VERTICAL.BOTTOM,
                }));
            }
        }
    };
    const updateHorizontalPosition = () => {
        if (menuPortalRef.current) {
            const itemElement = parent === 'SINGLE_ITEM' ? menuRef.current.closest('.c-tb-list-item-single__element') : menuRef.current;
            const { width: menuPortalWidth } = menuPortalRef.current.getBoundingClientRect();
            const { right: itemRightPosition } = itemElement.getBoundingClientRect();

            if (itemRightPosition + menuPortalWidth > window.innerWidth) {
                setMenuDirection((prevPosition) => ({
                    ...prevPosition,
                    horizontal: MENU_DIRECTION.HORIZONTAL.LEFT,
                }));
            } else {
                setMenuDirection((prevPosition) => ({
                    ...prevPosition,
                    horizontal: MENU_DIRECTION.HORIZONTAL.RIGHT,
                }));
            }
        }
    };
    const handleScroll = () => {
        if (portalRef.current) {
            portalRef.current.setPortalPosition(menuRef.current.getBoundingClientRect());
        }

        updateElementOverflow();
        updateVerticalPosition();
    };
    const handlePortalRef = (portal) => {
        if (portal !== portalRef.current && portal !== null) {
            portal.setPortalPosition(menuRef.current.getBoundingClientRect());
        }

        portalRef.current = portal;
    };

    useEffect(() => {
        isClosableRef.current = isClosable;
    }, [isClosable]);

    useEffect(() => {
        if (isExpanded) {
            window.document.addEventListener('click', toggleMenuOnClickOutside, false);
        }

        return () => {
            window.document.removeEventListener('click', toggleMenuOnClickOutside, false);
        };
    }, [isExpanded, toggleMenuOnClickOutside]);

    useEffect(() => {
        if (scrollWrapperRef && isExpanded) {
            scrollWrapperRef.current.addEventListener('scroll', handleScroll);

            return () => {
                scrollWrapperRef.current?.removeEventListener('scroll', handleScroll);
            };
        }
    }, [isExpanded, scrollWrapperRef]);

    useEffect(() => {
        if (isExpanded) {
            updateVerticalPosition();
            updateHorizontalPosition();
        }
    }, [isExpanded]);

    return (
        <div className={wrapperClassName} ref={menuRef}>
            <div className={togglerClassName} onClick={toggleMenu}>
                <Icon name="options" extraClasses={iconClassName} />
            </div>
            {isExpanded && !isItemCovered && (
                <Portal ref={handlePortalRef} extraClasses={portalClassName}>
                    <ContextualMenuContext.Provider value={menuContextData}>
                        <ActionList ref={menuPortalRef} item={item} parent={parent} extraClasses={actionListClassName} />
                    </ContextualMenuContext.Provider>
                </Portal>
            )}
        </div>
    );
};

ContextualMenu.propTypes = {
    item: PropTypes.object,
    isDisabled: PropTypes.bool,
    parent: PropTypes.string,
    isExpanded: PropTypes.bool.isRequired,
    setIsExpanded: PropTypes.func.isRequired,
    scrollWrapperRef: PropTypes.shape({ current: PropTypes.instanceOf(Element) }),
};

ContextualMenu.defaultProps = {
    item: {},
    isDisabled: false,
    parent: '',
    scrollWrapperRef: null,
};

export default ContextualMenu;
