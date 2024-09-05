import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

class Iframe extends PureComponent {
    constructor(props) {
        super(props);

        this._refIframe = null;
        this.state = {
            currentProps: {
                isVisible: props.isVisible,
                isLoading: props.isLoading,
            },
            isVisible: props.isVisible,
            isLoading: props.isLoading,
        };

        this.handleLoad = this.handleLoad.bind(this);
        this.setPreviewRef = this.setPreviewRef.bind(this);
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
        this.setState((state) => ({ ...state, isLoading: true }));
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.isLoading && !this.state.isLoading && this._refIframe) {
            this._refIframe.contentWindow.document.body.dispatchEvent(new CustomEvent('ibexa-inputs:recalculate-styling'));
        }
    }

    handleLoad() {
        this.props.onLoad(this._refIframe);

        this.setState((state) => ({ ...state, isLoading: false }));
    }

    setPreviewRef(iframe) {
        this._refIframe = iframe;
    }

    renderLoadingScreen() {
        if (!this.state.isLoading) {
            return null;
        }

        return (
            <div className="c-pb-iframe__loader-container">
                <div className="c-pb-iframe__loader" />
            </div>
        );
    }

    render() {
        const { id, name, src } = this.props;
        const attrs = {
            className: 'c-pb-iframe',
        };
        const iframeAttrs = {
            id,
            src,
            name,
            ref: this.setPreviewRef,
            onLoad: this.handleLoad,
            className: 'c-pb-iframe__preview',
            frameBorder: 0,
            hidden: !this.state.isVisible,
        };

        if (this.state.isLoading) {
            attrs.className = `${attrs.className} ${attrs.className}--is-loading`;
        }

        return (
            <div {...attrs}>
                {this.renderLoadingScreen()}
                <iframe {...iframeAttrs} />
            </div>
        );
    }
}

Iframe.propTypes = {
    id: PropTypes.string,
    name: PropTypes.string,
    title: PropTypes.string,
    onLoad: PropTypes.func.isRequired,
    src: PropTypes.string.isRequired,
    isVisible: PropTypes.bool,
    isLoading: PropTypes.bool.isRequired,
};

Iframe.defaultProps = {
    id: 'page-builder-preview',
    name: 'page-builder-preview',
    title: 'Page preview',
    isVisible: true,
};

export default Iframe;
