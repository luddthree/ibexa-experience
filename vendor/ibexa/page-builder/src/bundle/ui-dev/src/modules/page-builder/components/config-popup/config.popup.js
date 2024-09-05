import React, { Component } from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Translator } = window;

const CLASS_MODAL_OPEN = 'modal-open';
const MODAL_CONFIG = {
    backdrop: 'static',
    keyboard: true,
};

class ConfigPopup extends Component {
    constructor(props) {
        super(props);

        this._refModal = null;

        this.setModalRef = this.setModalRef.bind(this);
        this.onKeyUp = this.onKeyUp.bind(this);

        this.state = {
            currentProps: {
                isVisible: props.isVisible,
                isLoading: props.isLoading,
            },
            isVisible: props.isVisible,
            isLoading: props.isLoading,
        };
    }

    static getDerivedStateFromProps(nextProps, prevState) {
        if (nextProps.isVisible !== prevState.currentProps.isVisible || nextProps.isLoading !== prevState.currentProps.isLoading) {
            return {
                currentProps: {
                    isVisible: nextProps.isVisible,
                    isLoading: nextProps.isLoading,
                },
                isVisible: nextProps.isVisible,
                isLoading: nextProps.isLoading,
            };
        }

        return null;
    }

    componentDidMount() {
        const { isVisible: show } = this.state;

        if (show) {
            const bootstrapModal = window.bootstrap.Modal.getOrCreateInstance(this._refModal, {
                ...MODAL_CONFIG,
                focus: this.props.hasFocus,
            });

            bootstrapModal.show();

            this.attachModalEventHandlers();
        }

        window.ibexa.helpers.tooltips.parse();
    }

    componentDidUpdate() {
        const { isVisible: show } = this.state;

        const bootstrapModal = window.bootstrap.Modal.getOrCreateInstance(this._refModal, {
            ...MODAL_CONFIG,
            focus: this.props.hasFocus,
        });

        if (show) {
            bootstrapModal.show();
            this.attachModalEventHandlers();
        } else {
            bootstrapModal.hide();
        }
    }

    componentWillUnmount() {
        window.bootstrap.Modal.getOrCreateInstance(this._refModal).hide();
        document.body.classList.remove(CLASS_MODAL_OPEN);
    }

    attachModalEventHandlers() {
        this._refModal.addEventListener('keyup', this.onKeyUp);
        this._refModal.addEventListener('hidden.bs.modal', this.props.onClose);
    }

    onKeyUp(event) {
        const { originalEvent } = event;
        const escKeyCode = 27;
        const escKeyPressed = originalEvent && (originalEvent.which === escKeyCode || originalEvent.keyCode === escKeyCode);

        if (escKeyPressed) {
            this.props.onClose();
        }
    }

    setModalRef(component) {
        this._refModal = component;
    }

    renderSubtitle() {
        if (!this.hasSubtitle()) {
            return null;
        }

        return <span className="ibexa-modal__subheader c-pb-config-popup__subtitle">{this.props.type}</span>;
    }

    hasSubtitle() {
        const { type } = this.props;

        return !!type;
    }

    render() {
        const { isVisible } = this.state;
        const { additionalClasses } = this.props;
        const modalAttrs = {
            className: 'c-pb-config-popup modal fade ibexa-modal',
            ref: this.setModalRef,
            tabIndex: this.props.hasFocus ? '-1' : undefined,
        };
        const closeBtnLabel = Translator.trans(/*@Desc("Close")*/ 'config_popup.close.label', {}, 'ibexa_page_builder');

        document.body.classList.toggle(CLASS_MODAL_OPEN, isVisible);

        if (this.hasSubtitle()) {
            modalAttrs.className = `${modalAttrs.className} ibexa-modal--has-subtitle`;
        }

        if (additionalClasses) {
            modalAttrs.className = `${modalAttrs.className} ${additionalClasses}`;
        }

        return (
            <div {...modalAttrs}>
                <div className="modal-dialog c-pb-config-popup__dialog modal-lg" role="dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">{this.props.name}</h5>
                            <button
                                type="button"
                                className="close c-pb-config-popup__btn--close"
                                data-bs-dismiss="modal"
                                aria-label={closeBtnLabel}
                                onClick={this.props.onClose}
                                title={closeBtnLabel}
                            >
                                <Icon name="discard" extraClasses="ibexa-icon--small-medium" />
                            </button>
                        </div>
                        {this.renderSubtitle()}
                        <div className="modal-body c-pb-config-popup__body">{this.props.children}</div>
                        <div className="modal-footer">{this.props.footer}</div>
                    </div>
                </div>
            </div>
        );
    }
}

ConfigPopup.propTypes = {
    isVisible: PropTypes.bool,
    isLoading: PropTypes.bool,
    onClose: PropTypes.func.isRequired,
    children: PropTypes.node.isRequired,
    name: PropTypes.string.isRequired,
    type: PropTypes.string,
    hasFocus: PropTypes.bool,
    additionalClasses: PropTypes.string,
    footer: PropTypes.element,
};

ConfigPopup.defaultProps = {
    isVisible: false,
    isLoading: true,
    type: null,
    hasFocus: true,
    additionalClasses: null,
    footer: null,
};

export default ConfigPopup;
