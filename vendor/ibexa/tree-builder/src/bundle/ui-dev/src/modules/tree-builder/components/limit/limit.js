import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import { getTranslator } from '@ibexa-admin-ui/src/bundle/Resources/public/js/scripts/helpers/context.helper';

import IndentationHorizontal from '../indentation-horizontal/indentation.horizontal';
import { SubitemsLimitContext } from '../../tree.builder.module';

const Limit = ({ isExpanded, subitems, itemDepth }) => {
    const Translator = getTranslator();
    const subitemsLimit = useContext(SubitemsLimitContext);
    const subitemsLimitReached = subitems.length >= subitemsLimit;

    if (subitemsLimit === null || !isExpanded || !subitemsLimitReached) {
        return null;
    }

    const message = Translator.trans(
        /*@Desc("Loading limit reached")*/
        'show_more.limit_reached',
        {},
        'ibexa_tree_builder_ui',
    );

    return (
        <div className="c-tb-list-item-single__element c-tb-list-item-single__element--limit">
            <IndentationHorizontal itemDepth={itemDepth} />
            <span className="c-tb-list-item-single__load-more-limit-info">{message}</span>
        </div>
    );
};

Limit.propTypes = {
    isExpanded: PropTypes.bool.isRequired,
    itemDepth: PropTypes.number,
    subitems: PropTypes.array,
};

Limit.defaultProps = {
    itemDepth: 0,
    subitems: [],
};

export default Limit;
