import React, { useCallback } from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Translator } = window;

const LoadMoreBtn = ({ onLoadMore, isLoading }) => {
    const loadMoreButtonLabel = Translator.trans(/*@Desc("Load More")*/ 'calendar.load_more_btn.label', {}, 'ibexa_calendar_widget');
    const handleLoadMore = useCallback(() => {
        if (isLoading) {
            return;
        }

        onLoadMore();
    }, [onLoadMore, isLoading]);
    let loadingIcon = null;

    if (isLoading) {
        loadingIcon = <Icon name="spinner" extraClasses="c-load-more-btn__spinner ibexa-icon--medium ibexa-spin" />;
    }

    return (
        <button type="button" className="btn ibexa-btn ibexa-btn--secondary c-load-more-btn" onClick={handleLoadMore} disabled={isLoading}>
            {loadingIcon}
            {loadMoreButtonLabel}
        </button>
    );
};

LoadMoreBtn.propTypes = {
    onLoadMore: PropTypes.func.isRequired,
    isLoading: PropTypes.bool.isRequired,
};

export default LoadMoreBtn;
