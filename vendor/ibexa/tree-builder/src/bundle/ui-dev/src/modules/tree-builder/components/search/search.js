import React, { useContext, useState, useEffect } from 'react';
import PropTypes from 'prop-types';

import Icon from '../../../../../../../../../admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import { WidthContainerContext, checkIsTreeCollapsed } from '../width-container/width.container';

const { Translator } = window;

const Search = ({ onSearchInputChange, initialValue }) => {
    const [inputValue, setInputValue] = useState(initialValue);
    const [widthContainer] = useContext(WidthContainerContext);
    const containerWidth = widthContainer.resizedContainerWidth ?? widthContainer.containerWidth;
    const placeholder = Translator.trans(/*@Desc("Search...")*/ 'search.placeholder', {}, 'ibexa_tree_builder_ui');
    const clearSearch = () => {
        setInputValue('');
    };
    const isCollapsed = checkIsTreeCollapsed(containerWidth);

    useEffect(() => {
        onSearchInputChange(inputValue);
    }, [inputValue]);

    return (
        <div className="c-tb-search">
            {!isCollapsed && (
                <div className="ibexa-input-text-wrapper ibexa-input-text-wrapper--search">
                    <input
                        type="text"
                        className="form-control ibexa-input ibexa-input--text ibexa-input--small"
                        autoComplete="on"
                        tabIndex="1"
                        placeholder={placeholder}
                        onChange={(event) => setInputValue(event.target.value)}
                        value={inputValue}
                    />
                    <div className="ibexa-input-text-wrapper__actions">
                        <button
                            type="button"
                            className="btn ibexa-btn ibexa-btn--no-text ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--clear"
                            tabIndex="-1"
                            onClick={clearSearch}
                        >
                            <Icon name="discard" extraClasses="ibexa-icon--tiny" />
                        </button>
                        <button
                            type="button"
                            className="btn ibexa-btn ibexa-btn--no-text ibexa-input-text-wrapper__action-btn ibexa-input-text-wrapper__action-btn--search"
                            tabIndex="-1"
                        >
                            <Icon name="search" extraClasses="ibexa-icon ibexa-icon--small" />
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
};

Search.propTypes = {
    onSearchInputChange: PropTypes.func.isRequired,
    initialValue: PropTypes.string,
};

Search.defaultProps = {
    initialValue: '',
};

export default Search;
