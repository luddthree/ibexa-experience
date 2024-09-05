import React, { useState } from 'react';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import SidebarField from './sidebar.field';
import SidebarFieldsGroup from './sidebar.fields.group';

const { Translator } = window;
const title = Translator.trans(/*@Desc("Fields")*/ 'sidebar.fields', {}, 'ibexa_form_builder');

const Sidebar = (props) => {
    const [fieldFilterQuery, setFieldFilterQuery] = useState('');
    const renderSidebarField = ({ name, identifier, thumbnail }) => {
        const fieldFilterQueryLowerCase = fieldFilterQuery.toLowerCase();
        const fieldNameLowerCase = name.toLowerCase();
        const isHidden = !fieldNameLowerCase.includes(fieldFilterQueryLowerCase);

        return (
            <SidebarField
                key={identifier}
                name={name}
                type={identifier}
                thumbnail={thumbnail}
                isHidden={isHidden}
                onDragStart={props.fieldDragStart}
                onDragEnd={props.fieldDragEnd}
                appendNewField={props.appendNewField}
            />
        );
    };
    const renderSidebarSingleGroup = (category, isCollapsed) => {
        const categoryFormFields = props.fieldsConfigByCategory[category];

        return (
            <SidebarFieldsGroup key={category} title={category} isCollapsed={isCollapsed}>
                {categoryFormFields.map(renderSidebarField)}
            </SidebarFieldsGroup>
        );
    };
    const renderSidebarGroups = () => {
        const { fieldsConfigByCategory } = props;

        return Object.keys(fieldsConfigByCategory).map((category) => renderSidebarSingleGroup(category, false));
    };

    return (
        <div className="c-ibexa-fb-sidebar">
            <div className="c-ibexa-fb-sidebar__title-bar">
                <h3 className="c-ibexa-fb-sidebar__title">{title}</h3>
            </div>
            <div className="c-ibexa-fb-sidebar__search-bar">
                <input
                    type="text"
                    value={fieldFilterQuery}
                    onChange={(event) => setFieldFilterQuery(event.target.value)}
                    className="ibexa-input ibexa-input--text c-ibexa-fb-sidebar__sidebar-filter form-control"
                    placeholder={Translator.trans(/*@Desc("Search...")*/ 'sidebar_filter.placeholder', {}, 'ibexa_form_builder')}
                />
                <Icon name="search" extraClasses="ibexa-icon--small" />
            </div>
            <div className="c-ibexa-fb-sidebar__list">{renderSidebarGroups()}</div>
        </div>
    );
};

Sidebar.propTypes = {
    title: PropTypes.string.isRequired,
    fieldsConfigByCategory: PropTypes.object.isRequired,
    appendNewField: PropTypes.func.isRequired,
    fieldDragStart: PropTypes.func,
    fieldDragEnd: PropTypes.func,
};

Sidebar.defaultProps = {
    fieldDragStart: () => {},
    fieldDragEnd: () => {},
};

export default Sidebar;
