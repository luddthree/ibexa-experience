import React, { PureComponent } from 'react';
import FormBuilder from './form-builder/form.builder';
import DragDrop from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/core/drag.drop';
import ErrorBoundary from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/page-builder/components/error-boundary/error.boundary';

class FormBuilderModule extends PureComponent {
    constructor(props) {
        super(props);

        this._refFormBuilder = React.createRef();

        this.markInvalidFields = this.markInvalidFields.bind(this);
    }

    markInvalidFields(fields) {
        return this._refFormBuilder.current.markInvalidFields(fields);
    }

    render() {
        return (
            <ErrorBoundary>
                <DragDrop
                    render={(methods) => {
                        return <FormBuilder ref={this._refFormBuilder} {...this.props} {...methods} />;
                    }}
                />
            </ErrorBoundary>
        );
    }
}

export default FormBuilderModule;

window.ibexa.addConfig('modules.FormBuilder', FormBuilderModule);
