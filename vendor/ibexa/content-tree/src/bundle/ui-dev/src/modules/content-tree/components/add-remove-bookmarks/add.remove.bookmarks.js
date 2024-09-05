import React from 'react';
import PropTypes from 'prop-types';

import RemoveFromBookmarks from '../remove-from-bookmarks/remove.from.bookmarks';
import AddToBookmarks from '../add-to-bookmarks/add.to.bookmarks';

const AddRemoveBookmarks = ({ item }) => {
    if (item.internalItem?.isBookmarked) {
        return <RemoveFromBookmarks item={item} />;
    }

    return <AddToBookmarks item={item} />;
};

AddRemoveBookmarks.propTypes = {
    item: PropTypes.object,
};

AddRemoveBookmarks.defaultProps = {
    item: {},
};

export default AddRemoveBookmarks;
