import React, { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import SegmentItem from './segment.item';

const SEGMENTS_COUNT_TO_SHOW_MORE_BUTTON = 5;

const SegmentGroup = ({ segments, id, name, segmentFilterQueryLowerCase, onChange, moreButtonLabel }) => {
    const [isMoreButtonVisible, setIsMoreButtonVisible] = useState(segments.length > SEGMENTS_COUNT_TO_SHOW_MORE_BUTTON);
    const [isMoreButtonClicked, setIsMoreButtonClicked] = useState(false);
    const [isCollapsed, setIsCollapsed] = useState(false);
    const groupHidden = segments.every((segment) => {
        const lowerCaseSegmentName = segment.name.toLowerCase();
        return !lowerCaseSegmentName.includes(segmentFilterQueryLowerCase);
    });
    const segmentListClass = createCssClassNames({
        'c-segments__list': true,
        'c-segments__list--hidden': isCollapsed,
    });

    const segmentGroupClass = createCssClassNames({
        'c-segments__group': true,
        'c-segments__group--hidden': !segments.length || groupHidden,
    });
    const handleMoreButtonClick = () => {
        setIsMoreButtonVisible(false);
        setIsMoreButtonClicked(true);
    };
    const segmentsVisible = segments.filter((segment) => {
        const lowerCaseSegmentName = segment.name.toLowerCase();

        return lowerCaseSegmentName.includes(segmentFilterQueryLowerCase);
    });
    const handleCollapse = () => {
        setIsCollapsed(!isCollapsed);
    };

    useEffect(() => {
        if (!isMoreButtonClicked) {
            setIsMoreButtonVisible(segmentsVisible.length > SEGMENTS_COUNT_TO_SHOW_MORE_BUTTON);
        }
    }, [segmentsVisible, isMoreButtonClicked]);

    return (
        <div className={segmentGroupClass}>
            <h3 className="c-segments__group-name">
                <button className="c-segments__group-button" type="button" onClick={handleCollapse}>
                    {name}
                    <Icon
                        name={isCollapsed ? 'caret-down' : 'caret-up'}
                        extraClasses="c-segments__group-icon ibexa-icon--small ibexa-icon--toggle"
                    />
                </button>
            </h3>
            <ul className={segmentListClass}>
                {segments.map((segment) => {
                    const isVisible = segmentsVisible.some((segmentVisible) => segmentVisible.id === segment.id);
                    const segmentsVisibleIndex = segmentsVisible.findIndex((segmentVisible) => segmentVisible.id === segment.id);
                    const isHidden = !isVisible || (segmentsVisibleIndex > 4 && isMoreButtonVisible);

                    return <SegmentItem key={segment.id} id={segment.id} name={segment.name} isHidden={isHidden} onChange={onChange} />;
                })}
                {isMoreButtonVisible && (
                    <button className="c-segments__more-button" onClick={handleMoreButtonClick}>
                        <span className="c-segments__more-icon">+</span>
                        <span className="c-segments__more-label">{moreButtonLabel}</span>
                    </button>
                )}
            </ul>
        </div>
    );
};

SegmentGroup.propTypes = {
    segments: PropTypes.arrayOf(
        PropTypes.shape({
            id: PropTypes.number.isRequired,
            name: PropTypes.string.isRequired,
        }),
    ),
    id: PropTypes.number.isRequired,
    name: PropTypes.string.isRequired,
    onChange: PropTypes.func.isRequired,
    segmentFilterQueryLowerCase: PropTypes.string.isRequired,
    moreButtonLabel: PropTypes.string.isRequired,
};

export default SegmentGroup;
