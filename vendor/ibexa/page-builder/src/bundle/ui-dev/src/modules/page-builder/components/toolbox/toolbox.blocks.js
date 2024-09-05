import React, { useState } from 'react';
import PropTypes from 'prop-types';
import BlocksGroup from '../block/blocks.group';
import Block from '../block/block';

const { Translator } = window;

const BlocksToolbox = ({ blocksToRender, onBlockDrag, onBlockDragStart, isAddingBlocksEnabled }) => {
    const [blockFilterQuery, setBlockFilterQuery] = useState('');
    const updateBlockFilterQuery = ({ target }) => {
        setBlockFilterQuery(target.value);
    };
    const blockFilterQueryLowerCase = blockFilterQuery.toLowerCase();
    const blocksByCategories = blocksToRender.reduce((output, config) => {
        const blockNameLowerCase = config.name.toLowerCase();
        const isHidden = !blockNameLowerCase.includes(blockFilterQueryLowerCase);

        output[config.category] ??= [];
        output[config.category].push(
            <Block
                key={config.type}
                type={config.type}
                name={config.name}
                thumbnail={config.thumbnail}
                onDragStart={onBlockDragStart}
                onDrag={onBlockDrag}
                isDraggable={isAddingBlocksEnabled}
                isHidden={isHidden}
            />,
        );

        return output;
    }, {});

    const blockFilterPlaceholder = Translator.trans(/*@Desc("Search...")*/ 'block_filter.placeholder', {}, 'ibexa_page_builder');

    return (
        <>
            <div className="c-pb-toolbox__search-bar">
                <input
                    type="text"
                    name="filter"
                    placeholder={blockFilterPlaceholder}
                    value={blockFilterQuery}
                    onChange={updateBlockFilterQuery}
                    className="form-control ibexa-input ibexa-input--text c-pb-toolbox__toolbox-filter"
                />
            </div>
            <div className="c-pb-toolbox__list">
                {Object.keys(blocksByCategories).map((blockCategory) => (
                    <BlocksGroup title={blockCategory} key={`block-group-${blockCategory}`}>
                        {blocksByCategories[blockCategory]}
                    </BlocksGroup>
                ))}
            </div>
        </>
    );
};

BlocksToolbox.propTypes = {
    blocksToRender: PropTypes.arrayOf(PropTypes.shape({}).isRequired).isRequired,
    onBlockDrag: PropTypes.func.isRequired,
    onBlockDragStart: PropTypes.func.isRequired,
    isAddingBlocksEnabled: PropTypes.bool.isRequired,
};

export default BlocksToolbox;
