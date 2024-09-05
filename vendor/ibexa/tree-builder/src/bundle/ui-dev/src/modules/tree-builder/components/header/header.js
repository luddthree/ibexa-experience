import React, { useContext, useState } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import ContextualMenu from '../contextual-menu/contextual.menu';
import { WidthContainerContext, checkIsTreeCollapsed } from '../width-container/width.container';
import { MenuActionsContext, ResizableContext, ACTION_PARENT } from '../../tree.builder.module';

const COLLAPSED_WIDTH = 66;
const EXPANDED_WIDTH = 320;

const Header = ({ name, renderContent }) => {
    const { actionsVisible } = useContext(MenuActionsContext);
    const { isResizable } = useContext(ResizableContext);
    const [widthContainer, setWidthContainer] = useContext(WidthContainerContext);
    const [isExpanded, setIsExpanded] = useState(false);
    const containerWidth = widthContainer.resizedContainerWidth ?? widthContainer.containerWidth;
    const isCollapsed = checkIsTreeCollapsed(containerWidth);
    const toggleWidthContainer = () => {
        setWidthContainer((prevState) => ({
            ...prevState,
            containerWidth: isCollapsed ? EXPANDED_WIDTH : COLLAPSED_WIDTH,
        }));
    };
    const renderCollapseButton = () => {
        if (!isResizable) {
            return null;
        }

        const iconName = isCollapsed ? 'caret-next' : 'caret-back';
        const caretIconClassName = createCssClassNames({
            'ibexa-icon--tiny': isCollapsed,
            'ibexa-icon--small': !isCollapsed,
        });

        return (
            <button
                type="button"
                className="ibexa-btn btn ibexa-btn--no-text ibexa-btn--tertiary c-tb-header__toggle-btn"
                onClick={toggleWidthContainer}
            >
                {isCollapsed && <Icon name="content-tree" extraClasses="ibexa-icon--small" />}
                <Icon name={iconName} extraClasses={caretIconClassName} />
            </button>
        );
    };
    const renderHeaderContent = () => {
        if (renderContent) {
            return renderContent();
        }

        return (
            <div className="c-tb-header__name">
                <Icon name="content-tree" extraClasses="ibexa-icon--small c-tb-header__tree-icon" />
                <span className="c-tb-header__name-content">{name}</span>
            </div>
        );
    };

    return (
        <div className="c-tb-header">
            {renderCollapseButton()}
            {!isCollapsed && (
                <>
                    {renderHeaderContent()}
                    <div className="c-tb-header__options">
                        {actionsVisible && (
                            <ContextualMenu parent={ACTION_PARENT.TOP_MENU} isExpanded={isExpanded} setIsExpanded={setIsExpanded} />
                        )}
                    </div>
                </>
            )}
        </div>
    );
};

Header.propTypes = {
    name: PropTypes.string.isRequired,
    renderContent: PropTypes.func,
};

Header.defaultProps = {
    renderContent: null,
};

export default Header;
