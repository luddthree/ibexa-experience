import React, { PureComponent } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';

import Action from '../action/action';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import FieldConfigPanel from './field.config.panel';
import Iframe from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/page-builder/components/iframe/iframe';

const { Translator } = window;
const SELECTOR_POPUP_WRAPPER = '.m-ibexa-fb__popup-wrapper';

class FormField extends PureComponent {
    constructor(props) {
        super(props);

        this._refField = React.createRef();
        this._refConfigPopupComponent = React.createRef();
        this._refIframeComponent;
        this._refUdwContainer = window.document.querySelector('#react-udw');

        this.openFieldConfigPopup = this.openFieldConfigPopup.bind(this);
        this.remove = this.remove.bind(this);
        this.hideConfigPopup = this.hideConfigPopup.bind(this);
        this.handleDragStart = this.handleDragStart.bind(this);
        this.handleDragEnd = this.handleDragEnd.bind(this);
        this.getConfigIframe = this.getConfigIframe.bind(this);
        this.attachConfigPopupEventHandlers = this.attachConfigPopupEventHandlers.bind(this);
        this.handleOnConfigPopupIframeLoad = this.handleOnConfigPopupIframeLoad.bind(this);
        this.handleLoadAfterConfigFormSubmit = this.handleLoadAfterConfigFormSubmit.bind(this);
        this.setConfigIframeRef = this.setConfigIframeRef.bind(this);
        this.setConfigFormSubmittedState = this.setConfigFormSubmittedState.bind(this);
        this.openUdw = this.openUdw.bind(this);
        this.closeUdw = this.closeUdw.bind(this);
        this.updateFieldNameState = this.updateFieldNameState.bind(this);
        this.updateFieldNameOnClose = this.updateFieldNameOnClose.bind(this);

        this.state = {
            isConfigPopupVisible: false,
            shouldPopupWatchLoad: false,
            isConfigDataSent: false,
            udwConfig: null,
            nameFieldValue: props.name,
            isDragging: false,
        };
    }

    componentDidMount() {
        window.ibexa.helpers.tooltips.parse();
    }

    componentWillUnmount() {
        window.ibexa.helpers.tooltips.hideAll();
    }

    openFieldConfigPopup(event) {
        event.preventDefault();

        this.setState(
            () => ({
                isConfigPopupVisible: true,
                shouldPopupWatchLoad: true,
            }),
            () => this.props.onConfigPopupOpened(this.props.id),
        );
    }

    openUdw(event) {
        this.setState({
            udwConfig: {
                ...event.detail,
                onConfirm: (items) => {
                    event.detail.onConfirm(items);
                    this.closeUdw();
                },
                onCancel: () => {
                    event.detail.onCancel();
                    this.closeUdw();
                },
            },
        });
    }

    closeUdw() {
        this.setState({ udwConfig: null });
    }

    updateFieldNameState({ detail }) {
        const nameFieldValue = detail.name;

        this.props.checkCanSetFieldName({
            id: detail.id,
            value: nameFieldValue,
            successCallback: () => this.setState(() => ({ nameFieldValue }), detail.successCallback),
            errorCallback: detail.errorCallback,
        });
    }

    remove() {
        const { onRemove, id } = this.props;

        onRemove(id);
    }

    hideConfigPopup() {
        this.setState(
            () => ({
                isConfigPopupVisible: false,
            }),
            this.updateFieldNameOnClose,
        );
    }

    updateFieldNameOnClose() {
        const { nameFieldValue, isConfigDataSent } = this.state;

        if (isConfigDataSent) {
            return this.props.onNameChange(this.props.id, nameFieldValue);
        }

        this.setState(() => ({ nameFieldValue: this.props.name }));
    }

    handleDragStart(event) {
        const { onDragStart, id, identifier, name, views } = this.props;
        const image = document.querySelector(`[data-ibexa-sidebar-field-type="${identifier}"]`);

        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/html', image);
        event.dataTransfer.setDragImage(image, 0, 0);

        setTimeout(() => {
            this.setState({ isDragging: true });
        }, 0);
        onDragStart({ id, identifier, name, views });
    }

    handleDragEnd(event) {
        this.props.onDragEnd(event);
        this.setState({ isDragging: false });
    }

    getConfigIframe() {
        return this._refIframeComponent && this._refIframeComponent._refIframe;
    }

    getConfigPopup() {
        return this._refConfigPopupComponent.current && this._refConfigPopupComponent.current._refModal;
    }

    forceConfigPopupBackdropHidden() {
        const popup = this.getConfigPopup();

        if (popup) {
            window.bootstrap.Modal.getOrCreateInstance(popup).hide();
        }
    }

    allowScrolling() {
        document.body.classList.remove('modal-open', 'ibexa-non-scrollable');
    }

    attachConfigPopupEventHandlers() {
        if (this.state.isConfigDataSent) {
            return;
        }

        const iframeDoc = this.getConfigIframe().contentDocument;

        iframeDoc.body.addEventListener('ibexa-open-udw', this.openUdw, false);
        iframeDoc.body.addEventListener('ibexa-update-field-name', this.updateFieldNameState, false);
        iframeDoc.querySelector('[name="field_configuration"]').addEventListener('submit', this.setConfigFormSubmittedState, false);
        iframeDoc.querySelector('[data-form-action="discard"]').addEventListener('click', this.hideConfigPopup, false);
    }

    setConfigFormSubmittedState() {
        this.getConfigIframe().onload = this.handleLoadAfterConfigFormSubmit;
        this.setState(() => ({
            isConfigPopupVisible: true,
            isConfigDataSent: true,
            shouldPopupWatchLoad: true,
        }));
    }

    handleOnConfigPopupIframeLoad() {
        this.setState(
            () => ({
                isConfigDataSent: false,
                shouldPopupWatchLoad: false,
                isConfigPopupVisible: true,
            }),
            this.attachConfigPopupEventHandlers,
        );
    }

    handleLoadAfterConfigFormSubmit() {
        const iframe = this.getConfigIframe();
        const iframeDocument = iframe.contentDocument;
        const fieldData = iframeDocument.querySelector('[name="FieldConfiguration"]');

        if (!fieldData) {
            this.setState(
                () => ({
                    isConfigDataSent: false,
                    shouldPopupWatchLoad: false,
                }),
                this.attachConfigPopupEventHandlers,
            );

            return;
        }

        iframe.onload = null;

        this.setState(
            () => ({ isConfigPopupVisible: false, isConfigDataSent: false, shouldPopupWatchLoad: false }),
            () => {
                const field = JSON.parse(fieldData.content);

                this.props.onFieldDataUpdate(field);
                this.props.unmarkInvalidField(field);
            },
        );
    }

    renderConfigPopup() {
        const { isConfigDataSent, isConfigPopupVisible, nameFieldValue } = this.state;

        if (!isConfigPopupVisible) {
            this.forceConfigPopupBackdropHidden();
            this.allowScrolling();

            return null;
        }

        const { id } = this.props;
        const iframeAttrs = {
            ref: this.setConfigIframeRef,
            src: 'about:blank',
            id: id,
            name: id,
            title: id,
            onLoad: isConfigDataSent ? this.handleLoadAfterConfigFormSubmit : this.handleOnConfigPopupIframeLoad,
            isVisible: !isConfigDataSent,
        };

        return ReactDOM.createPortal(
            <FieldConfigPanel onCancel={this.hideConfigPopup} title={nameFieldValue} isClosed={!isConfigPopupVisible}>
                <Iframe {...iframeAttrs} fs={true} />
            </FieldConfigPanel>,
            document.querySelector(SELECTOR_POPUP_WRAPPER),
        );
    }

    setConfigIframeRef(ref) {
        this._refIframeComponent = ref;
    }

    render() {
        const { id, onDragOver, identifier, isHighlighted, isRemoved } = this.props;
        const { udwConfig } = this.state;
        const actionTitleSettings = Translator.trans(/*@Desc("Settings")*/ 'field.settings', {}, 'ibexa_form_builder');
        const actionTitleRemove = Translator.trans(/*@Desc("Remove")*/ 'field.remove', {}, 'ibexa_form_builder');
        const prefixFieldClassName = 'c-ibexa-fb-form-field';

        let fieldClassName = prefixFieldClassName;

        if (this.props.isInvalid) {
            fieldClassName = `${fieldClassName} ${prefixFieldClassName}--invalid`;
        }

        if (this.state.isDragging) {
            fieldClassName = `${fieldClassName} ${prefixFieldClassName}--is-dragging-out`;
        }

        if (isHighlighted) {
            fieldClassName = `${fieldClassName} ${prefixFieldClassName}--highlighted`;
        }

        if (isRemoved) {
            fieldClassName = `${fieldClassName} ${prefixFieldClassName}--removed`;
        }

        return (
            <>
                {udwConfig &&
                    ReactDOM.createPortal(React.createElement(window.ibexa.modules.UniversalDiscovery, udwConfig), this._refUdwContainer)}
                <div
                    ref={this._refField}
                    className={fieldClassName}
                    onDragStart={this.handleDragStart}
                    onDragOver={onDragOver}
                    onDragEnd={this.handleDragEnd}
                    data-ibexa-field-id={id}
                >
                    <div className="c-ibexa-fb-form-field__content" draggable="true">
                        <div className="c-ibexa-fb-form-field__left-col">
                            <div className="c-ibexa-fb-form-field__drag-handler">
                                <Icon name="drag" extraClasses="ibexa-icon--tiny-small" />
                            </div>
                            <div className="c-ibexa-fb-form-field__label">{this.state.nameFieldValue ?? identifier}</div>
                        </div>
                        <div className="c-ibexa-fb-form-field__right-col">
                            <div className="c-ibexa-fb-form-field__setting-wrapper">
                                <Action
                                    title={actionTitleSettings}
                                    onClick={this.openFieldConfigPopup}
                                    icon={<Icon name="settings-block" extraClasses="ibexa-icon--small" />}
                                />
                            </div>
                            <div className="c-ibexa-fb-form-field__trash-wrapper">
                                <Action
                                    title={actionTitleRemove}
                                    onClick={this.remove}
                                    icon={<Icon name="discard" extraClasses="ibexa-icon--small" />}
                                />
                            </div>
                        </div>
                        {this.renderConfigPopup()}
                    </div>
                </div>
            </>
        );
    }
}

FormField.propTypes = {
    id: PropTypes.string.isRequired,
    identifier: PropTypes.string.isRequired,
    fieldName: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
    views: PropTypes.object.isRequired,
    onRemove: PropTypes.func.isRequired,
    onDragStart: PropTypes.func.isRequired,
    onDragOver: PropTypes.func.isRequired,
    onDragEnd: PropTypes.func.isRequired,
    onConfigPopupOpened: PropTypes.func.isRequired,
    onFieldDataUpdate: PropTypes.func.isRequired,
    onNameChange: PropTypes.func.isRequired,
    isInvalid: PropTypes.bool.isRequired,
    checkCanSetFieldName: PropTypes.func.isRequired,
    isHighlighted: PropTypes.bool,
    isRemoved: PropTypes.bool,
    unmarkInvalidField: PropTypes.func.isRequired,
};

FormField.defaultProps = {
    isHighlighted: false,
    isRemoved: false,
};

export default FormField;
