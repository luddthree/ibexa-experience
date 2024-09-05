import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Alert from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/alert/alert';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import Popup from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/popup/popup.component.js';
import ConfigPanel, { CONFIG_PANEL_TYPE_LEFT, LEFT_PANEL_TYPES } from '../../../common/components/config-panel/config.panel';
import ConfigPanelBody from '../../../common/components/config-panel/config.panel.body';
import ConfigPanelFooter from '../../../common/components/config-panel/config.panel.footer';

const { Translator, ReactDOM } = window;
const CLOSE_CONFIG_PANEL_KEY = 'Escape';

class LayoutSelector extends Component {
    constructor(props) {
        super(props);

        this.renderLayoutItem = this.renderLayoutItem.bind(this);
        this.selectItem = this.selectItem.bind(this);
        this.handleKeyDown = this.handleKeyDown.bind(this);
        this.subtitle = Translator.trans(/*@Desc("Choose layout")*/ 'layout_selector.create.subtitle', {}, 'ibexa_page_builder');
        this.state = {
            currentProps: {
                itemSelectedId: props.itemSelectedId,
                isValidActiveLayout: props.isValidActiveLayout,
            },
            itemSelectedId: props.itemSelectedId,
            isValidActiveLayout: props.isValidActiveLayout,
        };
    }

    static getDerivedStateFromProps(nextProps, prevState) {
        if (
            nextProps.itemSelectedId !== prevState.currentProps.itemSelectedId ||
            nextProps.isValidActiveLayout !== prevState.currentProps.isValidActiveLayout
        ) {
            return {
                currentProps: {
                    itemSelectedId: nextProps.itemSelectedId,
                    isValidActiveLayout: nextProps.isValidActiveLayout,
                },
                itemSelectedId: nextProps.itemSelectedId,
                isValidActiveLayout: nextProps.isValidActiveLayout,
            };
        }

        return null;
    }

    componentDidMount() {
        document.body.addEventListener('click', this.closeConfigPanelByClickOutside);
        document.body.addEventListener('keyup', this.closeConfigPanelByKeyboard);
    }

    componentWillUnmount() {
        document.body.removeEventListener('click', this.closeConfigPanelByClickOutside);
        document.body.removeEventListener('keyup', this.closeConfigPanelByKeyboard);
    }

    closeConfigPanelByClickOutside({ target }) {
        if (target.classList.contains('ibexa-backdrop')) {
            this.props.onCancel();
        }
    }

    closeConfigPanelByKeyboard({ key }) {
        if (key === CLOSE_CONFIG_PANEL_KEY) {
            this.props.onCancel();
        }
    }

    selectItem(event) {
        this.setState({
            itemSelectedId: event.currentTarget.dataset.id,
            isValidActiveLayout: true,
        });
    }

    handleKeyDown(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            this.selectItem(event);
        }
    }

    renderLayoutItem(data) {
        if (!data.visible && data.id !== this.state.itemSelectedId) {
            return null;
        }
        const isSelected = data.id === this.state.itemSelectedId;
        const className = createCssClassNames({
            'c-pb-layout-selector__item': true,
            'c-pb-layout-selector__item--selected': isSelected,
            'c-pb-layout-selector__item--unavailable': !data.visible,
        });

        return (
            <div
                role="button"
                tabIndex="0"
                key={data.id}
                className={className}
                onClick={this.selectItem}
                onKeyDown={this.handleKeyDown}
                data-id={data.id}
                title={data.description}
            >
                <img className="c-pb-layout-selector__item-image" src={data.thumbnail} />
                <p className="c-pb-layout-selector__item-desc">{data.description}</p>
                <input
                    tabIndex="-1"
                    type="radio"
                    checked={isSelected}
                    className="c-pb-layout-selector__radio ibexa-input ibexa-input--radio"
                />
            </div>
        );
    }

    renderWarningMessage() {
        const warningMessage = Translator.trans(
            /*@Desc("Switching layout saves the current Page draft")*/ 'layout_selector.warning.message',
            {},
            'ibexa_page_builder',
        );

        return <Alert type="info" title={warningMessage} />;
    }

    renderErrorMessage() {
        const errorMessage = Translator.trans(
            /*@Desc("The layout of this landing page is no longer available and you cannot publish it. Please select a different layout.")*/ 'layout_selector.error.label',
            {},
            'ibexa_page_builder',
        );

        return <Alert type="error" title={errorMessage} />;
    }

    renderAlerts() {
        if (!this.props.displaySaveDraftWarning && this.state.isValidActiveLayout) {
            return null;
        }

        return this.state.isValidActiveLayout ? this.renderWarningMessage() : this.renderErrorMessage();
    }

    renderFooter() {
        const { isValidActiveLayout, itemSelectedId } = this.state;
        const { onCancel, onConfirm } = this.props;
        const forceSelection = !isValidActiveLayout;
        const cancelBtnLabel = Translator.trans(/*@Desc("Discard")*/ 'layout_selector.discard.label', {}, 'ibexa_page_builder');

        return (
            <>
                <button
                    type="button"
                    className="btn ibexa-btn ibexa-btn--filled-info"
                    disabled={!itemSelectedId}
                    onClick={() => onConfirm(itemSelectedId)}
                >
                    {this.props.confirmBtnLabel}
                </button>
                {!forceSelection && (
                    <button type="button" className="btn ibexa-btn ibexa-btn--info" onClick={onCancel}>
                        {cancelBtnLabel}
                    </button>
                )}
            </>
        );
    }

    actionBtnsConfig() {
        const { isValidActiveLayout, itemSelectedId } = this.state;
        const { onConfirm, confirmBtnLabel } = this.props;
        const forceSelection = !isValidActiveLayout;
        const cancelBtnLabel = Translator.trans(/*@Desc("Discard")*/ 'layout_selector.discard.label', {}, 'ibexa_page_builder');
        const btnsConfig = [
            {
                label: confirmBtnLabel,
                onClick: () => onConfirm(itemSelectedId),
                disabled: !itemSelectedId,
                className: 'ibexa-btn--filled-info',
            },
        ];

        if (!forceSelection) {
            btnsConfig.push({
                label: cancelBtnLabel,
                className: 'ibexa-btn--info',
            });
        }

        return btnsConfig;
    }

    renderPopup() {
        const { isValidActiveLayout } = this.state;
        const { title, layouts, isCreateMode, onCancel } = this.props;
        const forceSelection = !isValidActiveLayout;
        const forceFooterBtnsClick = forceSelection || isCreateMode;

        return (
            <Popup
                onClose={onCancel}
                isVisible={true}
                size="medium"
                actionBtnsConfig={this.actionBtnsConfig()}
                title={title}
                noCloseBtn={forceFooterBtnsClick}
                extraClasses="c-pb-layout-selector__popup"
                showTooltip={false}
            >
                {this.renderAlerts()}
                <div className="c-pb-layout-selector__subtitle">{this.subtitle}</div>
                <div className="c-pb-layout-selector__layouts">{layouts.map(this.renderLayoutItem)}</div>
            </Popup>
        );
    }

    renderConfigPanel() {
        const { title, layouts, onCancel, isVisible } = this.props;

        return (
            <ConfigPanel
                type={CONFIG_PANEL_TYPE_LEFT}
                leftPanelType={LEFT_PANEL_TYPES.LAYOUT_SELECTOR}
                extraClasses="ibexa-pb-config-panel--layout-switcher c-pb-layout-selector"
                onCancel={onCancel}
                isClosed={!isVisible}
                title={title}
            >
                <ConfigPanelBody extraClasses="c-pb-layout-selector__content">
                    {this.renderAlerts()}
                    <div className="c-pb-layout-selector__layouts">{layouts.map(this.renderLayoutItem)}</div>
                </ConfigPanelBody>
                <ConfigPanelFooter isClosed={!isVisible}>{this.renderFooter()}</ConfigPanelFooter>
            </ConfigPanel>
        );
    }

    render() {
        const { isCreateMode, modalContainer } = this.props;

        return ReactDOM.createPortal(isCreateMode ? this.renderPopup() : this.renderConfigPanel(), modalContainer);
    }
}

LayoutSelector.propTypes = {
    onCancel: PropTypes.func.isRequired,
    onConfirm: PropTypes.func.isRequired,
    layouts: PropTypes.array.isRequired,
    modalContainer: PropTypes.object.isRequired,
    title: PropTypes.string.isRequired,
    confirmBtnLabel: PropTypes.string.isRequired,
    itemSelectedId: PropTypes.string.isRequired,
    isValidActiveLayout: PropTypes.bool.isRequired,
    isCreateMode: PropTypes.bool.isRequired,
    displaySaveDraftWarning: PropTypes.bool.isRequired,
    isVisible: PropTypes.bool.isRequired,
};

export default LayoutSelector;
