import React from 'react';

const NameContent = ({ searchActive, searchValue, name }) => {
    if (!searchActive) {
        return name;
    }

    const searchNameRegexp = new RegExp(`(.*)(${searchValue})(.*)`, 'i');
    const [, searchPrefix, searchName, searchSuffix] = name.match(searchNameRegexp) ?? [];

    if (!searchName) {
        return name;
    }

    return (
        <>
            <span className="c-tt-list-item__search-fragment">{searchPrefix}</span>
            <span className="c-tt-list-item__search-fragment--matched">{searchName}</span>
            <span className="c-tt-list-item__search-fragment">{searchSuffix}</span>
        </>
    );
};

NameContent.propTypes = {
    searchActive: PropTypes.bool.isRequired,
    searchValue: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
};

export default NameContent;
