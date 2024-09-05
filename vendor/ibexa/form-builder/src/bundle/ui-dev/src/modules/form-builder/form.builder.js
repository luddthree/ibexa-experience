import React, { Component } from 'react';
import PropTypes from 'prop-types';

import generateGuid from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/guid-generator/guid.generator';
import deepClone from '@ibexa-page-builder/src/bundle/ui-dev/src/modules/helpers/deep.clone';
import removeFromArray from './helpers/remove.from.array';
import Sidebar from './components/sidebar/sidebar';
import FormField from './components/field/form.field';

const { Translator } = window;
const CLASS_PLACEHOLDER = 'droppable-placeholder';
const CLASS_WORKSPACE = 'm-ibexa-fb-workspace';
const SELECTOR_PLACEHOLDER = '.droppable-placeholder';
const ZONE_ID = 'workspace';
const IDENTIFIER_FIELD_DATA_ATTRIBUTE = 'ibexaFieldId';
const HIGHLIGHT_TIMEOUT = 3000;
const REMOVE_ACTIVE_ZONE_TIMEOUT = 400;
const nonDraggingState = {
    isDragging: false,
    draggedFieldData: null,
    draggedFromSidebar: null,
    dragOverZoneId: null,
};

class FormBuilder extends Component {
    constructor(props) {
        super(props);

        this._refWorkspace = React.createRef();
        this._refWorkspaceContent = React.createRef();
        this._refFieldConfigTextarea;
        this._refFieldConfigIdInput;

        this.handleDragStartSidebarField = this.handleDragStartSidebarField.bind(this);
        this.handleDragStartFormField = this.handleDragStartFormField.bind(this);
        this.handleDropSidebarField = this.handleDropSidebarField.bind(this);
        this.handleDropWorkspaceField = this.handleDropWorkspaceField.bind(this);
        this.handleElementDragOver = this.handleElementDragOver.bind(this);
        this.handleZoneDragOver = this.handleZoneDragOver.bind(this);
        this.handleZoneDragOut = this.handleZoneDragOut.bind(this);
        this.handleActiveZone = this.handleActiveZone.bind(this);
        this.getPlaceholderNodes = this.getPlaceholderNodes.bind(this);
        this.cancelDraggingState = this.cancelDraggingState.bind(this);
        this.handleDrop = this.handleDrop.bind(this);
        this.addPlaceholderInZone = this.addPlaceholderInZone.bind(this);
        this.removePlaceholders = this.removePlaceholders.bind(this);
        this.removePlaceholdersWithDelay = this.removePlaceholdersWithDelay.bind(this);
        this.renderFormField = this.renderFormField.bind(this);
        this.removeFieldFromFieldsState = this.removeFieldFromFieldsState.bind(this);
        this.updateFieldPosition = this.updateFieldPosition.bind(this);
        this.insertSiblingField = this.insertSiblingField.bind(this);
        this.updateFields = this.updateFields.bind(this);
        this.finishDropAction = this.finishDropAction.bind(this);
        this.requestFieldConfigForm = this.requestFieldConfigForm.bind(this);
        this.getFieldsState = this.getFieldsState.bind(this);
        this.updateFieldValue = this.updateFieldValue.bind(this);
        this.updateFieldNameValue = this.updateFieldNameValue.bind(this);
        this.checkCanSetFieldName = this.checkCanSetFieldName.bind(this);
        this.getNewFieldName = this.getNewFieldName.bind(this);
        this.appendNewField = this.appendNewField.bind(this);
        this.unmarkInvalidField = this.unmarkInvalidField.bind(this);

        const fieldsConfigByCategory = this.groupFormFieldsByCategory(this.props.fieldsConfig);

        this.state = {
            ...nonDraggingState,
            fields: props.fieldValue.fields,
            fieldsConfigByCategory,
            invalidFields: [],
            fieldFilterQuery: '',
            removedFieldsIds: [],
        };
    }

    componentDidMount() {
        this._refFieldConfigTextarea = this.props.fieldFormRequestConfig.querySelector('#request_field_configuration_form');
        this._refFieldConfigIdInput = this.props.fieldFormRequestConfig.querySelector('#request_field_configuration_field_id');

        window.document.body.dispatchEvent(new CustomEvent('ibexa-form-builder-loaded'));
    }

    componentWillUnmount() {
        window.document.body.dispatchEvent(new CustomEvent('ibexa-form-builder-unloaded'));
    }

    groupFormFieldsByCategory(formFields) {
        return formFields.reduce((formFieldsMap, formField) => {
            const formFieldCategory = formField.category;

            formFieldsMap[formFieldCategory] = formFieldsMap[formFieldCategory] || [];
            formFieldsMap[formFieldCategory].push(formField);

            return formFieldsMap;
        }, {});
    }

    handleDragStartSidebarField(draggedFieldData) {
        this.setState(() => ({ isDragging: true, draggedFieldData, draggedFromSidebar: true }));
    }

    handleDragStartFormField(draggedFieldData) {
        this.setState(() => ({ isDragging: true, draggedFieldData, draggedFromSidebar: false }));
    }

    handleZoneDragOver(event) {
        event.preventDefault();

        const { target } = event;
        const { dragOverZoneId, isDragging } = this.state;
        const isDropZone = target === this._refWorkspaceContent.current;
        const shouldAddPlaceholder = isDropZone && !dragOverZoneId && isDragging;

        if (shouldAddPlaceholder) {
            this.setState(
                () => ({ dragOverZoneId: target.dataset.ezZoneId }),
                () => this.addPlaceholderInZone(target),
            );
        }

        return false;
    }

    handleActiveZone() {
        window.clearTimeout(this.activeZoneTimeout);
        this._refWorkspace.current.classList.add(`${CLASS_WORKSPACE}--active`);

        this.activeZoneTimeout = window.setTimeout(() => {
            this._refWorkspace.current.classList.remove(`${CLASS_WORKSPACE}--active`);
        }, REMOVE_ACTIVE_ZONE_TIMEOUT);
    }

    handleZoneDragOut(event) {
        if (event.target.isSameNode(this._refWorkspace.current)) {
            this.removePlaceholders();
            this.setState({ ...this.props.getInitDragDropState() });
        }
    }

    getFormFieldElement(target) {
        return this.props.getElement(target, (element) => element.dataset && element.dataset[IDENTIFIER_FIELD_DATA_ATTRIBUTE]);
    }

    getPlaceholderPreviewNode() {
        return this._refWorkspaceContent.current.querySelector(SELECTOR_PLACEHOLDER);
    }

    removeFieldFromFieldsState(fieldId) {
        this.setState((state) => ({
            removedFieldsIds: [...state.removedFieldsIds, fieldId],
        }));
    }

    insertSiblingField(fields, fieldData, siblingFieldId, insertBefore) {
        const besideFieldIndex = fields.findIndex((element) => element.id === siblingFieldId);

        if (besideFieldIndex === -1) {
            const message = Translator.trans(
                /*@Desc("Moved field data does not exist.")*/ 'moved.field.data.not.exists',
                {},
                'ibexa_form_builder',
            );

            window.ibexa.helpers.notification.showErrorNotification(message);

            return fields;
        }

        const insertionIndex = insertBefore ? besideFieldIndex : besideFieldIndex + 1;

        fields.splice(insertionIndex, 0, fieldData);

        return fields;
    }

    appendFormField(fields, fieldData) {
        fields.push(fieldData);

        return fields;
    }

    findByFieldId(fieldId) {
        return (field) => field.id === fieldId;
    }

    getNewFieldName(fieldName) {
        let index = 1;
        const { fields } = this.state;
        const generateFieldName = (name) => {
            const isNameUsed = fields.find((field) => field.name === name);

            if (!isNameUsed) {
                return name;
            }

            const numbersInName = name.match(/(#)\d+$/);

            if (numbersInName) {
                name = name.substring(0, numbersInName.index).trim();
            }

            index++;

            return generateFieldName(`${name} #${index}`);
        };

        return generateFieldName(fieldName);
    }

    getNewFieldData(fieldIdentifier) {
        const { fieldsConfig } = this.props;
        const fieldConfig = fieldsConfig.find((field) => field.identifier === fieldIdentifier);
        const fieldName = this.getNewFieldName(fieldConfig.name);
        const newField = {
            id: generateGuid('fbf-'),
            identifier: fieldConfig.identifier,
            name: fieldName,
            attributes: fieldConfig.attributes.map((attr) => ({
                id: generateGuid('fbfa-'),
                name: attr.id,
                type: attr.type,
                identifier: attr.identifier,
                value: attr.values || attr.defaultValue || '',
            })),
            validators: fieldConfig.validators || [],
        };

        return newField;
    }

    appendNewField(identifier) {
        const fieldData = this.getNewFieldData(identifier);

        this.setState(
            (state) => ({ fields: this.appendFormField(deepClone(state.fields), fieldData) }),
            this.setFormFieldTypeFormFieldValue,
        );
    }

    handleDropSidebarField({ siblingFieldId, insertBefore }) {
        const fieldData = this.getNewFieldData(this.state.draggedFieldData.identifier);
        const initialData = { ...this.props.getInitDragDropState() };

        this.setState(
            (state) => ({
                ...initialData,
                highlightedFieldId: fieldData.id,
                fields: this.updateFields(deepClone(state.fields), fieldData, siblingFieldId, insertBefore),
            }),
            this.finishDropAction,
        );
    }

    handleDropWorkspaceField({ siblingFieldId, insertBefore }) {
        const fieldId = this.state.draggedFieldData.id;

        if (fieldId === siblingFieldId) {
            return;
        }

        const initialData = { ...this.props.getInitDragDropState() };

        this.setState(
            (state) => ({
                ...initialData,
                highlightedFieldId: fieldId,
                fields: this.updateFieldPosition(deepClone(state.fields), fieldId, siblingFieldId, insertBefore),
            }),
            this.finishDropAction,
        );
    }

    updateFieldPosition(fields, fieldId, siblingFieldId, insertBefore) {
        const findCallback = this.findByFieldId(fieldId);
        const fieldData = fields.find(findCallback);

        if (!fieldData) {
            return fields;
        }

        return this.updateFields(removeFromArray(fields, findCallback), fieldData, siblingFieldId, insertBefore);
    }

    updateFields(fields, fieldData, siblingFieldId, insertBefore) {
        if (siblingFieldId) {
            return this.insertSiblingField(fields, fieldData, siblingFieldId, insertBefore);
        }

        return this.appendFormField(fields, fieldData);
    }

    addPlaceholderInZone(target) {
        const placeholders = [...this.getPlaceholderNodes()];

        if (placeholders.length !== 0 && this.state.fields.length !== 0 && target.isSameNode(this._refWorkspaceContent.current)) {
            return;
        }

        this.props.addPlaceholderInZone(target, placeholders, IDENTIFIER_FIELD_DATA_ATTRIBUTE, this.removePlaceholdersWithDelay);
    }

    removePlaceholders() {
        this.props.removePlaceholders(this.getPlaceholderNodes(), this.props.removePlaceholderWithoutAnimation);
    }

    removePlaceholdersWithDelay() {
        this.props.removePlaceholdersAfterTimeout(this.getPlaceholderNodes, this.cancelDraggingState);
    }

    getPlaceholderNodes() {
        return this._refWorkspaceContent.current.querySelectorAll(SELECTOR_PLACEHOLDER);
    }

    cancelDraggingState() {
        this.setState(() => ({ ...nonDraggingState }));
    }

    handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();

        const placeholder = this.getPlaceholderPreviewNode();

        if (!placeholder) {
            this.finishDropAction();

            return;
        }

        const { position: placeholderPosition, ibexaFieldId: siblingFieldId } = placeholder.dataset;
        const insertBefore = placeholderPosition === 'top';
        const isFieldIdDefined = siblingFieldId && siblingFieldId !== 'undefined';
        const placeholderDropData = {
            insertBefore,
            siblingFieldId: isFieldIdDefined ? siblingFieldId : null,
            isNew: this.state.draggedFromSidebar,
        };

        if (this.state.draggedFromSidebar) {
            this.handleDropSidebarField(placeholderDropData);
        } else {
            this.handleDropWorkspaceField(placeholderDropData);
        }
    }

    finishDropAction() {
        this.cancelDraggingState();
        this.setFormFieldTypeFormFieldValue();

        window.setTimeout(() => {
            this.setState({ highlightedFieldId: null });
        }, HIGHLIGHT_TIMEOUT);
    }

    setFormFieldTypeFormFieldValue() {
        const changeEvent = new Event('change');

        this.props.fieldValueInput.value = JSON.stringify({ fields: this.state.fields });
        this.props.fieldValueInput.dispatchEvent(changeEvent);
    }

    handleElementDragOver({ target, clientY, currentTarget }) {
        const formFieldElement = this.getFormFieldElement(target);
        const isPlaceholder = currentTarget.classList.contains(CLASS_PLACEHOLDER);

        if (!formFieldElement || isPlaceholder || !this.state.isDragging) {
            return;
        }

        const placeholders = this.getPlaceholderNodes();
        const { placeholderPosition, placeholderElementId, placeholderZoneId } = this.state;
        const placeholderMeta = this.props.addPlaceholderBesideElement(
            formFieldElement,
            clientY,
            placeholders,
            IDENTIFIER_FIELD_DATA_ATTRIBUTE,
            this.handleElementDragOver,
            { placeholderPosition, placeholderElementId, placeholderZoneId },
        );

        this.setState(() => ({ ...placeholderMeta }));
    }

    requestFieldConfigForm(fieldId) {
        this.props.fieldFormRequestConfig.target = fieldId;
        this._refFieldConfigTextarea.value = this.props.fieldValueInput.value;
        this._refFieldConfigIdInput.value = fieldId;
        this.props.fieldFormRequestConfig.submit();
    }

    getFieldsState() {
        return deepClone(this.state.fields);
    }

    updateFieldValue(fieldData) {
        const fields = this.getFieldsState().map((field) => {
            return field.id === fieldData.id ? { ...fieldData } : { ...field };
        });

        this.setState(() => ({ fields }), this.setFormFieldTypeFormFieldValue);
    }

    checkCanSetFieldName({ id, value, successCallback, errorCallback }) {
        const isNameUsed = this.state.fields.find((field) => field.name === value && field.id !== id);

        if (isNameUsed) {
            return errorCallback();
        }

        successCallback();
    }

    updateFieldNameValue(fieldId, name) {
        const fields = this.getFieldsState().map((field) => (field.id === fieldId ? { ...field, name } : { ...field }));

        this.setState(() => ({ fields }), this.setFormFieldTypeFormFieldValue);
    }

    markInvalidFields(invalidFields) {
        this.setState(() => ({ invalidFields }));
    }

    unmarkInvalidField(field) {
        this.setState((state) => ({
            invalidFields: state.invalidFields.filter((invalidField) => invalidField.id !== field.id),
        }));
    }

    checkIsFieldInvalid(field) {
        return !!this.state.invalidFields.find((invalidField) => invalidField.id === field.id);
    }

    renderFormField(config) {
        const isRemoved = this.state.removedFieldsIds.includes(config.id);

        return (
            <FormField
                key={config.id}
                {...config}
                fieldName={window.ibexa.formBuilder.config.fieldsConfig.find((data) => data.identifier === config.identifier).name}
                onRemove={this.removeFieldFromFieldsState}
                onDragOver={this.handleElementDragOver}
                onDragStart={this.handleDragStartFormField}
                onDragEnd={this.removePlaceholders}
                onConfigPopupOpened={this.requestFieldConfigForm}
                onFieldDataUpdate={this.updateFieldValue}
                onNameChange={this.updateFieldNameValue}
                checkCanSetFieldName={this.checkCanSetFieldName}
                isInvalid={this.checkIsFieldInvalid(config)}
                isHighlighted={config.id === this.state.highlightedFieldId}
                isRemoved={isRemoved}
                unmarkInvalidField={this.unmarkInvalidField}
                ref={(element) => {
                    if (element) {
                        element._refField.current.addEventListener('animationend', () => {
                            if (isRemoved) {
                                this.setState(
                                    (state) => ({
                                        fields: removeFromArray(deepClone(state.fields), this.findByFieldId(config.id)),
                                        removedFieldsIds: state.removedFieldsIds.filter((fieldId) => fieldId != config.id),
                                    }),
                                    this.setFormFieldTypeFormFieldValue,
                                );
                            }
                        });
                    }
                }}
            />
        );
    }

    render() {
        const { fields, isDragging, fieldsConfigByCategory } = this.state;
        const showEmptyMessage = !fields.length && !isDragging;
        let workspaceClassName = `${CLASS_WORKSPACE}__content`;
        const emptyMessage = Translator.trans(
            /*@Desc("Create a form by dragging elements here")*/ 'drag.drop.call.to.action',
            {},
            'ibexa_form_builder',
        );
        const emptyZoneHeadline = Translator.trans(/*@Desc("Build your form")*/ 'drag.drop.empty.zone.headline', {}, 'ibexa_form_builder');
        const emptyZoneMessage = Translator.trans(
            /*@Desc("Drag and drop item from library to build form")*/ 'drag.drop.empty.zone.message',
            {},
            'ibexa_form_builder',
        );

        if (showEmptyMessage) {
            workspaceClassName = `${workspaceClassName} ${CLASS_WORKSPACE}__content--empty`;
        }

        return (
            <div className="m-ibexa-fb">
                <div className="m-ibexa-fb__workspace-wrapper">
                    <div
                        className={CLASS_WORKSPACE}
                        onDragOver={(event) => {
                            event.preventDefault();

                            this.handleZoneDragOut(event);
                            this.handleActiveZone();

                            return false;
                        }}
                        ref={this._refWorkspace}
                    >
                        <div
                            className={workspaceClassName}
                            data-empty-message={emptyMessage}
                            ref={this._refWorkspaceContent}
                            data-ibexa-zone-id={ZONE_ID}
                            onDrop={this.handleDrop}
                            onDragOver={this.handleZoneDragOver}
                        >
                            <div className="m-ibexa-fb-workspace__empty-zone-placeholder">
                                <img
                                    className="m-ibexa-fb-workspace__empty-zone-image"
                                    src="/bundles/ibexaadminui/img/empty-field-definition-group.svg"
                                    alt={emptyZoneMessage}
                                />
                                <h2 className="m-ibexa-fb-workspace__empty-zone-headline">{emptyZoneHeadline}</h2>
                                <div className="m-ibexa-fb-workspace__empty-zone-label">{emptyZoneMessage}</div>
                            </div>
                            {fields.map(this.renderFormField)}
                        </div>
                    </div>
                </div>
                <div className="m-ibexa-fb__sidebar-wrapper">
                    <Sidebar
                        fieldsConfigByCategory={fieldsConfigByCategory}
                        fieldDragStart={this.handleDragStartSidebarField}
                        fieldDragEnd={() => {
                            this.cancelDraggingState();
                            this.removePlaceholders();
                        }}
                        appendNewField={this.appendNewField}
                    />
                </div>
                <div className="m-ibexa-fb__popup-wrapper" />
            </div>
        );
    }
}

FormBuilder.propTypes = {
    fieldValue: PropTypes.shape({
        fields: PropTypes.array.isRequired,
    }).isRequired,
    fieldValueInput: PropTypes.element.isRequired,
    fieldFormRequestConfig: PropTypes.element.isRequired,
    fieldsConfig: PropTypes.arrayOf(
        PropTypes.shape({
            attributes: PropTypes.array.isRequired,
            category: PropTypes.string.isRequired,
            identifier: PropTypes.string.isRequired,
            name: PropTypes.string.isRequired,
            thumbnail: PropTypes.string.isRequired,
        }),
    ).isRequired,
    getElement: PropTypes.func.isRequired,
    getInitDragDropState: PropTypes.func.isRequired,
    addPlaceholderInZone: PropTypes.func.isRequired,
    addPlaceholderBesideElement: PropTypes.func.isRequired,
    removePlaceholders: PropTypes.func.isRequired,
    removePlaceholdersAfterTimeout: PropTypes.func.isRequired,
    removePlaceholderWithoutAnimation: PropTypes.func.isRequired,
};

export default FormBuilder;
