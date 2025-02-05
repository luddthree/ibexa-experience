/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */
/**
 * @module utils
 */
export { default as env } from './env';
export { default as diff } from './diff';
export { default as fastDiff } from './fastdiff';
export { default as diffToChanges } from './difftochanges';
export { default as mix } from './mix';
export { default as EmitterMixin } from './emittermixin';
export { default as EventInfo } from './eventinfo';
export { default as ObservableMixin } from './observablemixin';
export { default as CKEditorError, logError, logWarning } from './ckeditorerror';
export { default as ElementReplacer } from './elementreplacer';
export { default as abortableDebounce } from './abortabledebounce';
export { default as count } from './count';
export { default as compareArrays } from './comparearrays';
export { default as createElement } from './dom/createelement';
export { default as Config } from './config';
export { default as isIterable } from './isiterable';
export { default as DomEmitterMixin } from './dom/emittermixin';
export { default as findClosestScrollableAncestor } from './dom/findclosestscrollableancestor';
export { default as global } from './dom/global';
export { default as getAncestors } from './dom/getancestors';
export { default as getDataFromElement } from './dom/getdatafromelement';
export { default as getBorderWidths } from './dom/getborderwidths';
export { default as isText } from './dom/istext';
export { default as Rect } from './dom/rect';
export { default as ResizeObserver } from './dom/resizeobserver';
export { default as setDataInElement } from './dom/setdatainelement';
export { default as toUnit } from './dom/tounit';
export { default as indexOf } from './dom/indexof';
export { default as insertAt } from './dom/insertat';
export { default as isComment } from './dom/iscomment';
export { default as isNode } from './dom/isnode';
export { default as isRange } from './dom/isrange';
export { default as isValidAttributeName } from './dom/isvalidattributename';
export { default as isVisible } from './dom/isvisible';
export { getOptimalPosition } from './dom/position';
export { default as remove } from './dom/remove';
export * from './dom/scroll';
export * from './keyboard';
export * from './language';
export { default as Locale } from './locale';
export { default as Collection } from './collection';
export { default as first } from './first';
export { default as FocusTracker } from './focustracker';
export { default as KeystrokeHandler } from './keystrokehandler';
export { default as toArray } from './toarray';
export { default as toMap } from './tomap';
export { default as priorities } from './priorities';
export { default as retry, exponentialDelay } from './retry';
export { default as insertToPriorityArray } from './inserttopriorityarray';
export { default as spliceArray } from './splicearray';
export { default as uid } from './uid';
export { default as delay } from './delay';
export { default as verifyLicense } from './verifylicense';
export { default as wait } from './wait';
export * from './unicode';
export { default as version, releaseDate } from './version';
