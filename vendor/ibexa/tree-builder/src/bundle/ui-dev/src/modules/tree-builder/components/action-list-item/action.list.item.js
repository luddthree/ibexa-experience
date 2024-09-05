import React, { useContext } from 'react';
import PropTypes from 'prop-types';

import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

import { IntermediateActionContext } from '../intermediate-action-provider/intermediate.action.provider';
import { ForcedPropsContext } from '../action-list/action.list';

const getProps = (props, forcedProps = {}) => {
    return Object.entries(props).reduce((output, [key, value]) => {
        output[key] = forcedProps[key] ?? value;

        return output;
    }, {});
};

const ActionItem = (props) => {
    const forcedProps = useContext(ForcedPropsContext);
    const { intermediateAction } = useContext(IntermediateActionContext);
    const { label, labelIcon, useIconAsLabel, isLoading, isDisabled, extraClasses, onClick, isCustomClose } = getProps(props, forcedProps);
    const getLabel = () => {
        if (useIconAsLabel && labelIcon) {
            return (
                <span title={label}>
                    <Icon name={labelIcon} extraClasses="ibexa-icon ibexa-icon--small" />
                </span>
            );
        }

        return label;
    };
    const getLoader = () => {
        const loaderClassName = createCssClassNames({
            'c-tb-action-list__item-loader': true,
            'c-tb-action-list__item-loader--hidden': !isLoading,
        });

        return (
            <div className={loaderClassName}>
                <div className="c-tb-action-list__item-spinner" />
            </div>
        );
    };
    const className = createCssClassNames({
        'c-tb-action-list__item': true,
        'c-tb-action-list__item--disabled': isDisabled || intermediateAction.isActive,
        [extraClasses]: extraClasses !== '',
    });
    const attrs = {
        className,
    };

    if (!isDisabled) {
        attrs.onClick = onClick;
    }

    if (isCustomClose) {
        attrs['data-custom-close'] = 1;
    }

    return (
        <li {...attrs}>
            {getLabel()}
            {getLoader()}
        </li>
    );
};

ActionItem.propTypes = {
    label: PropTypes.node.isRequired,
    extraClasses: PropTypes.string,
    isDisabled: PropTypes.bool,
    labelIcon: PropTypes.string,
    onClick: PropTypes.func,
    useIconAsLabel: PropTypes.bool,
    isCustomClose: PropTypes.bool,
};

ActionItem.defaultProps = {
    extraClasses: '',
    isDisabled: false,
    labelIcon: null,
    onClick: () => {},
    useIconAsLabel: false,
    isCustomClose: false,
};

export default ActionItem;
