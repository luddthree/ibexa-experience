import React from 'react';
import PropTypes from 'prop-types';

const SidebarHeader = ({ children }) => {
    return <div className="c-ip-sidebar-header">{children}</div>;
};

SidebarHeader.propTypes = {
    children: PropTypes.node.isRequired,
};

export default SidebarHeader;
