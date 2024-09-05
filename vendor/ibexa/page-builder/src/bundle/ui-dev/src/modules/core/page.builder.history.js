import deepClone from '../helpers/deep.clone';
import { POSITION_CHANGE_METHOD } from '../page-builder/page.builder';

export const ACTIONS = {
    ADD: 'ADD',
    DELETE: 'DELETE',
    REORDER: 'REORDER',
    UPDATE: 'UPDATE',
};

const getReorderedItem = (beforeArr, afterArr) => {
    const leftIndex = afterArr.findIndex((blockId, blockIndex) => beforeArr.indexOf(blockId) !== blockIndex);
    const rightIndex = afterArr.findLastIndex((blockId, blockIndex) => beforeArr.indexOf(blockId) !== blockIndex);

    if (beforeArr[leftIndex] === afterArr[rightIndex]) {
        return beforeArr[leftIndex];
    }

    return beforeArr[rightIndex];
};

export const findInsertBlockPosition = ({ currBlocksIds, prevBlocksIds }) => {
    const index = prevBlocksIds.findIndex((blockId) => !currBlocksIds.includes(blockId));

    return index;
};

export const findReorderBlockPositions = ({ block, prevBlocksIds, zoneKey, added, deleted }) => {
    if (added && deleted) {
        const destinationPosition = deleted.prevBlocksIds.findIndex((blockId) => blockId === block.id);

        return {
            sourceZoneKey: added.zoneKey,
            destinationZoneKey: deleted.zoneKey,
            destinationPosition: destinationPosition,
            positionChangeMethod: POSITION_CHANGE_METHOD.DRAG,
        };
    }

    const destinationPosition = prevBlocksIds.findIndex((blockId) => blockId === block.id);

    return {
        sourceZoneKey: zoneKey,
        destinationZoneKey: zoneKey,
        destinationPosition,
        positionChangeMethod: POSITION_CHANGE_METHOD.DRAG,
    };
};

export default class PageBuilderHistory {
    constructor(initialEntry) {
        this.stateChangesPrev = [];
        this.stateChangesNext = [];
        this.lastEntry = JSON.parse(initialEntry);
    }

    getLastChanges() {
        return this.stateChangesPrev[this.stateChangesPrev.length - 1];
    }

    getNextChanges() {
        return this.stateChangesNext[this.stateChangesNext.length - 1];
    }

    getBlocksZonesMap(zones) {
        const output = {};

        zones.forEach((zone, zoneKey) => {
            zone.blocks.forEach(({ id }) => {
                output[id] = zoneKey;
            });
        });

        return output;
    }

    getPrevAndCurrBlocksIds(zones, zoneKey) {
        const prevZoneBlocks = this.lastEntry.zones[zoneKey]?.blocks ?? [];
        const currZoneBlocks = zones[zoneKey].blocks;
        const prevBlocksIds = prevZoneBlocks.map(({ id }) => id);
        const currBlocksIds = currZoneBlocks.map(({ id }) => id);

        return {
            prevZoneBlocks,
            currZoneBlocks,
            prevBlocksIds,
            currBlocksIds,
        };
    }

    getDiffForNewBlock(zones, prevBlockZonesMap, currBlockZonesMap) {
        const [blockId, zoneKey] = Object.entries(currBlockZonesMap).find(([currBlockId]) => prevBlockZonesMap[currBlockId] === undefined);
        const { currZoneBlocks, prevBlocksIds, currBlocksIds } = this.getPrevAndCurrBlocksIds(zones, zoneKey);
        const newBlock = currZoneBlocks.find(({ id }) => id === blockId);

        return {
            action: ACTIONS.ADD,
            block: newBlock,
            prevBlocksIds,
            currBlocksIds,
            zoneKey,
        };
    }

    getDiffForDeletedBlock(zones, prevBlockZonesMap, currBlockZonesMap) {
        const [blockId, zoneKey] = Object.entries(prevBlockZonesMap).find(([prevBlockId]) => currBlockZonesMap[prevBlockId] === undefined);
        const { prevZoneBlocks, prevBlocksIds, currBlocksIds } = this.getPrevAndCurrBlocksIds(zones, zoneKey);
        const deletedBlock = prevZoneBlocks.find(({ id }) => id === blockId);

        return {
            action: ACTIONS.DELETE,
            block: deletedBlock,
            prevBlocksIds,
            currBlocksIds,
            zoneKey,
        };
    }

    getDiffForReorderedBlock(zones, prevBlockZonesMap, currBlockZonesMap) {
        const movedBetweenZonesBlockMetadata = Object.entries(prevBlockZonesMap).find(
            ([blockKey, zoneKey]) => currBlockZonesMap[blockKey] !== zoneKey,
        );

        if (movedBetweenZonesBlockMetadata) {
            const [blockId, prevZoneKey] = movedBetweenZonesBlockMetadata;
            const currZoneKey = currBlockZonesMap[blockId];
            const deletedBlockZoneData = this.getPrevAndCurrBlocksIds(zones, prevZoneKey);
            const addedBlockZoneData = this.getPrevAndCurrBlocksIds(zones, currZoneKey);
            const block = zones[currZoneKey].blocks.find(({ id }) => id === blockId);

            return {
                action: ACTIONS.REORDER,
                block,
                deleted: {
                    zoneKey: prevZoneKey,
                    prevBlocksIds: deletedBlockZoneData.prevBlocksIds,
                    currBlocksIds: deletedBlockZoneData.currBlocksIds,
                },
                added: {
                    zoneKey: currZoneKey,
                    prevBlocksIds: addedBlockZoneData.prevBlocksIds,
                    currBlocksIds: addedBlockZoneData.currBlocksIds,
                },
            };
        }

        let output;

        for (const zoneKey in zones) {
            const { prevBlocksIds, currBlocksIds } = this.getPrevAndCurrBlocksIds(zones, zoneKey);
            const reorderedBlockId = getReorderedItem(prevBlocksIds, currBlocksIds);

            if (reorderedBlockId) {
                const block = zones[zoneKey].blocks.find(({ id }) => id === reorderedBlockId);

                output = {
                    action: ACTIONS.REORDER,
                    block,
                    prevBlocksIds,
                    currBlocksIds,
                    zoneKey,
                };

                break;
            }
        }

        return output;
    }

    getDiffForUpdatedBlock(zones) {
        let updatedBlock;
        let zoneKey;

        for (zoneKey in this.lastEntry.zones) {
            updatedBlock = this.lastEntry.zones[zoneKey].blocks.find((prevBlock, prevBlockKey) => {
                const currBlock = zones[zoneKey].blocks[prevBlockKey];
                const prevBlockStringified = JSON.stringify(prevBlock);
                const currBlockStringified = JSON.stringify(currBlock);

                return prevBlockStringified !== currBlockStringified;
            });

            if (updatedBlock) {
                break;
            }
        }

        return {
            action: ACTIONS.UPDATE,
            block: updatedBlock,
            zoneKey,
        };
    }

    getDiff(entry) {
        const prevBlockZonesMap = this.getBlocksZonesMap(this.lastEntry.zones);
        const currBlockZonesMap = this.getBlocksZonesMap(entry.zones);
        const prevBlocksCount = Object.entries(prevBlockZonesMap).length;
        const currBlocksCount = Object.entries(currBlockZonesMap).length;

        if (prevBlocksCount < currBlocksCount) {
            return this.getDiffForNewBlock(entry.zones, prevBlockZonesMap, currBlockZonesMap);
        } else if (prevBlocksCount > currBlocksCount) {
            return this.getDiffForDeletedBlock(entry.zones, prevBlockZonesMap, currBlockZonesMap);
        }

        const reorderedData = this.getDiffForReorderedBlock(entry.zones, prevBlockZonesMap, currBlockZonesMap);

        if (reorderedData) {
            return reorderedData;
        }

        return this.getDiffForUpdatedBlock(entry.zones);
    }

    addEntry(newEntry) {
        if (this.lastEntry.layout != newEntry.layout) {
            return;
        }

        const newChangesEntry = this.getDiff(newEntry);

        if (!newChangesEntry.block) {
            return;
        }

        this.stateChangesPrev.push(newChangesEntry);
        this.lastEntry = newEntry;
        this.stateChangesNext = [];
    }

    getRevertedEntryFromAddDiff(lastEntry, lastChange) {
        const newZoneBlocks = lastEntry.zones[lastChange.zoneKey].blocks.filter(({ id }) => id !== lastChange.block.id);

        lastEntry.zones[lastChange.zoneKey].blocks = newZoneBlocks;

        return lastEntry;
    }

    getRevertedEntryFromDeleteDiff(lastEntry, lastChange) {
        const zoneBlocks = lastEntry.zones[lastChange.zoneKey].blocks;
        const newZoneBlocks = lastChange.prevBlocksIds.map((blockId) => {
            const oldBlock = zoneBlocks.find(({ id }) => id === blockId);

            return oldBlock ?? lastChange.block;
        });

        lastEntry.zones[lastChange.zoneKey].blocks = newZoneBlocks;

        return lastEntry;
    }

    getRevertedEntryFromReorderDiff(lastEntry, lastChange) {
        if (lastChange.added && lastChange.deleted) {
            const addedZoneBlocks = lastEntry.zones[lastChange.added.zoneKey].blocks;
            const deletedZoneBlocks = lastEntry.zones[lastChange.deleted.zoneKey].blocks;
            const addedNewZoneBlocks = lastChange.added.prevBlocksIds.map((blockId) => {
                return addedZoneBlocks.find(({ id }) => id === blockId);
            });
            const deletedNewZoneBlocks = lastChange.deleted.prevBlocksIds.map((blockId) => {
                const block = deletedZoneBlocks.find(({ id }) => id === blockId);

                if (block) {
                    return block;
                }

                return addedZoneBlocks.find(({ id }) => id === blockId);
            });

            lastEntry.zones[lastChange.added.zoneKey].blocks = addedNewZoneBlocks;
            lastEntry.zones[lastChange.deleted.zoneKey].blocks = deletedNewZoneBlocks;

            return lastEntry;
        }

        const zoneBlocks = lastEntry.zones[lastChange.zoneKey].blocks;
        const newZoneBlocks = lastChange.prevBlocksIds.map((blockId) => {
            return zoneBlocks.find(({ id }) => id === blockId);
        });

        lastEntry.zones[lastChange.zoneKey].blocks = newZoneBlocks;

        return lastEntry;
    }

    getRevertedEntryFromUpdateDiff(lastEntry, lastChange) {
        const zoneBlocks = lastEntry.zones[lastChange.zoneKey].blocks;
        const updatedBlockIndex = zoneBlocks.findIndex(({ id }) => id === lastChange.block.id);

        lastEntry.zones[lastChange.zoneKey].blocks[updatedBlockIndex] = lastChange.block;

        return lastEntry;
    }

    getRevertedEntry(lastChange) {
        const lastEntry = deepClone(this.lastEntry);

        switch (lastChange.action) {
            case ACTIONS.ADD:
                return this.getRevertedEntryFromAddDiff(lastEntry, lastChange);
            case ACTIONS.DELETE:
                return this.getRevertedEntryFromDeleteDiff(lastEntry, lastChange);
            case ACTIONS.REORDER:
                return this.getRevertedEntryFromReorderDiff(lastEntry, lastChange);
            case ACTIONS.UPDATE:
                return this.getRevertedEntryFromUpdateDiff(lastEntry, lastChange);
        }
    }

    get isPrevEmpty() {
        return this.stateChangesPrev.length === 0;
    }

    get isNextEmpty() {
        return this.stateChangesNext.length === 0;
    }

    undo(callback) {
        const lastChanges = this.getLastChanges();
        const lastEntry = this.getRevertedEntry(lastChanges);

        try {
            const actionStatus = callback({ value: lastEntry, diff: lastChanges });

            if (!actionStatus) {
                throw new Error(`Something has gone wrong, callback action didn't finish succesfully!`);
            }
        } catch (error) {
            console.error(error);

            return;
        }

        const newChangesEntry = this.getDiff(lastEntry);

        this.lastEntry = lastEntry;

        this.stateChangesPrev.pop();
        this.stateChangesNext.push(newChangesEntry);
    }

    redo(callback) {
        const nextChanges = this.getNextChanges();
        const lastEntry = this.getRevertedEntry(nextChanges);

        try {
            const actionStatus = callback({ value: lastEntry, diff: nextChanges });

            if (!actionStatus) {
                throw new Error(`Something has gone wrong, callback action didn't finish succesfully!`);
            }
        } catch (error) {
            console.error(error);

            return;
        }

        const newChangesEntry = this.getDiff(lastEntry);

        this.lastEntry = lastEntry;

        this.stateChangesNext.pop();
        this.stateChangesPrev.push(newChangesEntry);
    }
}
