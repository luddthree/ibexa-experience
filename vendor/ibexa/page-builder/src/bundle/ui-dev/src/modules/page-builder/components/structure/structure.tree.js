import React, { useRef, useEffect, useMemo } from 'react';
import PropTypes from 'prop-types';
import StructureMenuAction from './actions/menu.action.js';

import { ACTION_TYPE, ACTION_PARENT } from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/tree.builder.module';
import Delete from '@ibexa-tree-builder/src/bundle/ui-dev/src/modules/tree-builder/actions/delete/delete';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { ibexa, Translator } = window;
const MODULE_ID = 'ibexa-page-builder-tree';
const ROOT_ITEM_ID = '0';
const userId = ibexa.helpers.user.getId();

const StructureTree = ({ zones, scrollTo, hoverIn, hoverOut, blocksIdMap }) => {
    const treeBuilderModuleRef = useRef(null);
    const tree = useMemo(() => {
        const treeZonesStructure = zones.map(({ id: zoneId, blocks }, index) => {
            const dropZoneLabel = Translator.trans(
                /*@Desc("Drop zone %number%")*/ 'structure.drop.zone',
                {
                    number: index + 1,
                },
                'ibexa_page_builder',
            );
            const treeBlocksStructure = blocks.map(({ id: blockId, name, icon }) => {
                const blockName = blocksIdMap.get(blockId);
                const label = (
                    <div className="c-pb-structure-block">
                        {icon && (
                            <div className="c-pb-structure-block__icon">
                                <Icon customPath={icon} extraClasses="ibexa-icon ibexa-icon--small" />
                            </div>
                        )}
                        <span className="c-pb-structure-block__label">
                            <span className="c-pb-structure-block__block-name">{blockName}</span>: {name}
                        </span>
                    </div>
                );

                return {
                    id: blockId,
                    label,
                    total: 0,
                    isContainer: false,
                    internalItem: {},
                    onItemClick: () => scrollTo(blockId),
                    onItemHoverIn: () => hoverIn(blockId),
                    onItemHoverOut: () => hoverOut(blockId),
                    customAttrs: {
                        'data-block-id': blockId,
                    },
                };
            });

            return {
                id: zoneId,
                name: dropZoneLabel,
                label: dropZoneLabel,
                total: treeBlocksStructure.length,
                isContainer: true,
                subitems: treeBlocksStructure,
                actionsDisabled: true,
                dragItemDisabled: true,
            };
        });

        return {
            subitems: treeZonesStructure,
            total: treeZonesStructure.length,
            id: ROOT_ITEM_ID,
            isContainer: false,
        };
    }, [zones, blocksIdMap, scrollTo, hoverIn, hoverOut]);
    const blockToZonesMap = useMemo(
        () => new Map(zones.flatMap(({ blocks }, zoneIndex) => blocks.map(({ id: blockId }) => [blockId, zoneIndex]))),
        [zones],
    );
    const callbackDeleteElements = ({ selectedData }) => {
        const blockId = selectedData[0].id;

        document.body.dispatchEvent(
            new CustomEvent('ibexa-pb-blocks:action', {
                detail: { blockId, action: 'remove' },
            }),
        );

        return Promise.resolve(selectedData);
    };
    const callbackMoveElements = ({ nextIndex, nextParent, sibling, siblingPosition }, { selectedData }) => {
        const eventDetails = {
            blockId: selectedData[0].id,
            action: 'move',
        };

        if (nextIndex === -1) {
            const destinationZoneKey = zones.findIndex(({ id }) => id === nextParent.id);
            const destinationZone = zones[destinationZoneKey];
            const destinationPosition = destinationZone.blocks.length;

            eventDetails.destinationZoneKey = destinationZoneKey;
            eventDetails.destinationPosition = destinationPosition;

            treeBuilderModuleRef.current?.expandItems([{ id: nextParent.id }]);
        } else {
            const nextBlockId = sibling.id;
            const destinationZoneKey = blockToZonesMap.get(nextBlockId);
            let destinationPosition = zones[destinationZoneKey].blocks.findIndex(({ id }) => id === nextBlockId);

            if (siblingPosition !== 'prev') {
                destinationPosition += 1;
            }

            eventDetails.destinationZoneKey = destinationZoneKey;
            eventDetails.destinationPosition = destinationPosition;
        }

        document.body.dispatchEvent(
            new CustomEvent('ibexa-pb-blocks:move', {
                detail: eventDetails,
            }),
        );

        return Promise.resolve(selectedData);
    };
    useEffect(() => {
        const treeZonesStructureIds = tree.subitems
            .filter(({ subitems }) => subitems.length)
            .map((zone) => ({
                id: zone.id,
            }));
        const itemsToExpand = [{ id: ROOT_ITEM_ID }, ...treeZonesStructureIds];

        treeBuilderModuleRef.current?.expandItems(itemsToExpand);
    }, []);

    return (
        <div className="c-pb-toolbox-structure">
            <ibexa.modules.TreeBuilder
                ref={treeBuilderModuleRef}
                headerVisible={false}
                tree={tree}
                isActive={() => false}
                moduleId={MODULE_ID}
                userId={userId}
                selectionDisabled={true}
                rootElementDisabled={true}
                actionsType={ACTION_TYPE.CONTEXTUAL_MENU}
                callbackDeleteElements={callbackDeleteElements}
                callbackMoveElements={callbackMoveElements}
                isResizable={false}
                forceDefaultTheme={true}
            />
        </div>
    );
};

const treeBuilderConfig = {
    [MODULE_ID]: {
        menuActions: [
            {
                priority: 20,
                id: 'modifying',
                subitems: [
                    {
                        priority: 10,
                        id: 'moveUp',
                        eventType: 'move',
                        action: 'move-up',
                        component: StructureMenuAction,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        itemLabel: Translator.trans(/*@Desc("Move up")*/ 'structure.action.move.up', {}, 'ibexa_page_builder'),
                    },
                    {
                        priority: 20,
                        id: 'moveDown',
                        eventType: 'move',
                        action: 'move-down',
                        component: StructureMenuAction,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        itemLabel: Translator.trans(/*@Desc("Move down")*/ 'structure.action.move.down', {}, 'ibexa_page_builder'),
                    },
                    {
                        priority: 30,
                        id: 'configure',
                        eventType: 'action',
                        action: 'configure',
                        component: StructureMenuAction,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        itemLabel: Translator.trans(/*@Desc("Configuration")*/ 'structure.action.configuration', {}, 'ibexa_page_builder'),
                    },
                    {
                        priority: 40,
                        id: 'duplicate',
                        eventType: 'action',
                        action: 'duplicate',
                        component: StructureMenuAction,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        itemLabel: Translator.trans(/*@Desc("Duplicate")*/ 'structure.action.duplicate', {}, 'ibexa_page_builder'),
                    },
                    {
                        priority: 50,
                        id: 'refresh',
                        eventType: 'action',
                        action: 'refresh',
                        component: StructureMenuAction,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        itemLabel: Translator.trans(/*@Desc("Refresh")*/ 'structure.action.refresh', {}, 'ibexa_page_builder'),
                    },
                ],
            },
            {
                priority: 40,
                id: 'deleting',
                subitems: [
                    {
                        priority: 60,
                        id: 'delete',
                        component: Delete,
                        visibleIn: [ACTION_PARENT.SINGLE_ITEM],
                        isModalDisabled: true,
                    },
                ],
            },
        ],
    },
};

StructureTree.propTypes = {
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
    scrollTo: PropTypes.func.isRequired,
    hoverIn: PropTypes.func.isRequired,
    hoverOut: PropTypes.func.isRequired,
    blocksIdMap: PropTypes.instanceOf(Map).isRequired,
};

window.ibexa.addConfig('treeBuilder', treeBuilderConfig);
window.ibexa.addConfig('modules.StructureTree', StructureTree);

export default StructureTree;
