import React from 'react';
import PropTypes from 'prop-types';

import ActionList from '../action-list/action.list';

const ListMenu = ({ item, isDisabled, parent }) => {
    return (
        <div className="c-tb-list-menu">
            <ActionList item={item} isDisabled={isDisabled} parent={parent} useIconAsLabel={true} />
        </div>
    );
};

ListMenu.propTypes = {
    item: PropTypes.object,
    isDisabled: PropTypes.bool,
    parent: PropTypes.string,
};

ListMenu.defaultProps = {
    item: {},
    isDisabled: false,
    parent: '',
};

export default ListMenu;
