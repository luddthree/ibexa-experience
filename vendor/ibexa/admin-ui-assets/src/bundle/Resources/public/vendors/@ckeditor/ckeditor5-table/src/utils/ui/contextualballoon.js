/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */
/**
 * @module table/utils/ui/contextualballoon
 */
import { Rect } from 'ckeditor5/src/utils';
import { BalloonPanelView } from 'ckeditor5/src/ui';
import { getSelectionAffectedTableWidget, getTableWidgetAncestor } from './widget';
import { getSelectionAffectedTable } from '../common';
const DEFAULT_BALLOON_POSITIONS = BalloonPanelView.defaultPositions;
const BALLOON_POSITIONS = [
    DEFAULT_BALLOON_POSITIONS.northArrowSouth,
    DEFAULT_BALLOON_POSITIONS.northArrowSouthWest,
    DEFAULT_BALLOON_POSITIONS.northArrowSouthEast,
    DEFAULT_BALLOON_POSITIONS.southArrowNorth,
    DEFAULT_BALLOON_POSITIONS.southArrowNorthWest,
    DEFAULT_BALLOON_POSITIONS.southArrowNorthEast,
    DEFAULT_BALLOON_POSITIONS.viewportStickyNorth
];
/**
 * A helper utility that positions the
 * {@link module:ui/panel/balloon/contextualballoon~ContextualBalloon contextual balloon} instance
 * with respect to the table in the editor content, if one is selected.
 *
 * @param editor The editor instance.
 * @param target Either "cell" or "table". Determines the target the balloon will be attached to.
 */
export function repositionContextualBalloon(editor, target) {
    const balloon = editor.plugins.get('ContextualBalloon');
    const selection = editor.editing.view.document.selection;
    let position;
    if (target === 'cell') {
        if (getTableWidgetAncestor(selection)) {
            position = getBalloonCellPositionData(editor);
        }
    }
    else if (getSelectionAffectedTableWidget(selection)) {
        position = getBalloonTablePositionData(editor);
    }
    if (position) {
        balloon.updatePosition(position);
    }
}
/**
 * Returns the positioning options that control the geometry of the
 * {@link module:ui/panel/balloon/contextualballoon~ContextualBalloon contextual balloon} with respect
 * to the selected table in the editor content.
 *
 * @param editor The editor instance.
 */
export function getBalloonTablePositionData(editor) {
    const selection = editor.model.document.selection;
    const modelTable = getSelectionAffectedTable(selection);
    const viewTable = editor.editing.mapper.toViewElement(modelTable);
    return {
        target: editor.editing.view.domConverter.mapViewToDom(viewTable),
        positions: BALLOON_POSITIONS
    };
}
/**
 * Returns the positioning options that control the geometry of the
 * {@link module:ui/panel/balloon/contextualballoon~ContextualBalloon contextual balloon} with respect
 * to the selected table cell in the editor content.
 *
 * @param editor The editor instance.
 */
export function getBalloonCellPositionData(editor) {
    const mapper = editor.editing.mapper;
    const domConverter = editor.editing.view.domConverter;
    const selection = editor.model.document.selection;
    if (selection.rangeCount > 1) {
        return {
            target: () => createBoundingRect(selection.getRanges(), editor),
            positions: BALLOON_POSITIONS
        };
    }
    const modelTableCell = getTableCellAtPosition(selection.getFirstPosition());
    const viewTableCell = mapper.toViewElement(modelTableCell);
    return {
        target: domConverter.mapViewToDom(viewTableCell),
        positions: BALLOON_POSITIONS
    };
}
/**
 * Returns the first selected table cell from a multi-cell or in-cell selection.
 *
 * @param position Document position.
 */
function getTableCellAtPosition(position) {
    const isTableCellSelected = position.nodeAfter && position.nodeAfter.is('element', 'tableCell');
    return isTableCellSelected ? position.nodeAfter : position.findAncestor('tableCell');
}
/**
 * Returns bounding rectangle for given model ranges.
 *
 * @param ranges Model ranges that the bounding rect should be returned for.
 * @param editor The editor instance.
 */
function createBoundingRect(ranges, editor) {
    const mapper = editor.editing.mapper;
    const domConverter = editor.editing.view.domConverter;
    const rects = Array.from(ranges).map(range => {
        const modelTableCell = getTableCellAtPosition(range.start);
        const viewTableCell = mapper.toViewElement(modelTableCell);
        return new Rect(domConverter.mapViewToDom(viewTableCell));
    });
    return Rect.getBoundingRect(rects);
}
