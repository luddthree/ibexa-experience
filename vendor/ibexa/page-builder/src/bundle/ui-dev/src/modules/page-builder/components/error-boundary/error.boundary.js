import React, { Component } from 'react';
import PropTypes from 'prop-types';

const { Translator } = window;

class ErrorBoundary extends Component {
    constructor(props) {
        super(props);

        this.state = { hasError: false };
    }

    static getDerivedStateFromError() {
        return { hasError: true };
    }

    componentDidCatch(error, info) {
        console.error('catch:error', error, info);
    }

    render() {
        const errorMessage = Translator.trans(/*@Desc("Something went wrong")*/ 'error.message', {}, 'ibexa_page_builder');

        if (this.state.hasError) {
            return <h1>{errorMessage}</h1>;
        }

        return this.props.children;
    }
}

ErrorBoundary.propTypes = {
    children: PropTypes.element.isRequired,
};

export default ErrorBoundary;
