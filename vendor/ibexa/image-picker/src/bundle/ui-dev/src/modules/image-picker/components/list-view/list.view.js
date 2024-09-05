import React from 'react';
import PropTypes from 'prop-types';

import ListViewHeader from './list.view.header';
import ListViewItem from './list.view.item';

const ListView = ({ items }) => {
    return (
        <div className="c-ip-list-view">
            <table className="ibexa-table table">
                <ListViewHeader />
                <tbody className="ibexa-table__body">
                    {items.map((item) => (
                        <ListViewItem key={item.id} item={item} />
                    ))}
                </tbody>
            </table>
        </div>
    );
};

ListView.propTypes = {
    items: PropTypes.array.isRequired,
};

export default ListView;
