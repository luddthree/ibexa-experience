import React from 'react';
import PropTypes from 'prop-types';

import StructureTree from '../structure/structure.tree.js';

const StructureToolbox = ({ fieldValue, scrollTo, hoverIn, hoverOut, blocksIdMap, blocksIconMap }) => {
    const zonesToRender = fieldValue.zones.map(({ id, blocks, label }) => ({
        id,
        label,
        blocks: blocks.map(({ id: blockId, type, name }) => {
            const blockIcon = blocksIconMap.get(type);

            return {
                type,
                name,
                id: blockId,
                icon: blockIcon,
            };
        }),
    }));

    return <StructureTree zones={zonesToRender} scrollTo={scrollTo} hoverIn={hoverIn} hoverOut={hoverOut} blocksIdMap={blocksIdMap} />;
};

StructureToolbox.propTypes = {
    fieldValue: PropTypes.shape({
        layout: PropTypes.string.isRequired,
        zones: PropTypes.arrayOf(
            PropTypes.shape({
                id: PropTypes.string.isRequired,
                blocks: PropTypes.arrayOf(
                    PropTypes.shape({
                        name: PropTypes.string.isRequired,
                        type: PropTypes.string.isRequired,
                        id: PropTypes.string.isRequired,
                    }),
                ),
            }).isRequired,
        ).isRequired,
    }).isRequired,
    scrollTo: PropTypes.func.isRequired,
    hoverIn: PropTypes.func.isRequired,
    hoverOut: PropTypes.func.isRequired,
    blocksIdMap: PropTypes.instanceOf(Map).isRequired,
    blocksIconMap: PropTypes.instanceOf(Map).isRequired,
};

export default StructureToolbox;
