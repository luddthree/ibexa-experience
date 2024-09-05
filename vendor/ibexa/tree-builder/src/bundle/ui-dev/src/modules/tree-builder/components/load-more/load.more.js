import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import IndentationHorizontal from '../indentation-horizontal/indentation.horizontal';
import { SubitemsLimitContext } from '../../tree.builder.module';

import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

const LoadMore = ({ isExpanded, isLoading, subitems, loadMore, totalSubitemsCount, itemDepth }) => {
    const Translator = getTranslator();
    const subitemsLimit = useContext(SubitemsLimitContext);
    const subitemsLimitReached = subitems.length >= subitemsLimit;
    const allSubitemsLoaded = subitems.length === totalSubitemsCount;

    if (!isExpanded || subitemsLimitReached || allSubitemsLoaded || !subitems.length) {
        return null;
    }

    const seeMoreLabel = Translator.trans(/*@Desc("See more")*/ 'see_more', {}, 'ibexa_tree_builder_ui');
    const loadingMoreLabel = Translator.trans(/*@Desc("Loading more...")*/ 'loading_more', {}, 'ibexa_tree_builder_ui');
    const btnLabel = isLoading ? loadingMoreLabel : seeMoreLabel;
    let loadingSpinner = null;

    if (isLoading) {
        loadingSpinner = <Icon name="spinner" extraClasses="ibexa-spin ibexa-icon--small c-tb-list-item-single__load-more-btn-spinner" />;
    }

    return (
        <div className="c-tb-list-item-single__element c-tb-list-item-single__element--load-more">
            <IndentationHorizontal itemDepth={itemDepth} />
            <a className="c-tb-list-item-single__load-more" onClick={loadMore}>
                {loadingSpinner} {btnLabel}
            </a>
        </div>
    );
};

LoadMore.propTypes = {
    isExpanded: PropTypes.bool.isRequired,
    isLoading: PropTypes.bool.isRequired,
    loadMore: PropTypes.func.isRequired,
    totalSubitemsCount: PropTypes.number.isRequired,
    itemDepth: PropTypes.number,
    subitems: PropTypes.array,
};

LoadMore.defaultProps = {
    itemDepth: 0,
    subitems: [],
};

export default LoadMore;
