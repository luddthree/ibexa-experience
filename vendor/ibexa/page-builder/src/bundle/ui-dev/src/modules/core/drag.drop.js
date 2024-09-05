import { Component } from 'react';
import PropTypes from 'prop-types';

const CLASS_PLACEHOLDER = 'droppable-placeholder';
const CLASS_PLACEHOLDER_REMOVING = 'droppable-placeholder--removing';
const SELECTOR_ZONE = '[data-ibexa-zone-id]';
const PLACEHOLDER_POSITION_TOP = 'top';
const TIMEOUT_REMOVE_PLACEHOLDERS = 500;

class DragDrop extends Component {
    constructor(props) {
        super(props);

        this.onDragOverTimeout = null;
        this.placeholderRemovalTimeout = null;

        this.getElement = this.getElement.bind(this);
        this.getInitDragDropState = this.getInitDragDropState.bind(this);
        this.scrollContainer = this.scrollContainer.bind(this);
        this.removePlaceholderWithAnimation = this.removePlaceholderWithAnimation.bind(this);
        this.removePlaceholderWithoutAnimation = this.removePlaceholderWithoutAnimation.bind(this);
        this.createPlaceholder = this.createPlaceholder.bind(this);
        this.insertAfter = this.insertAfter.bind(this);
        this.insertBefore = this.insertBefore.bind(this);
        this.addPlaceholderBesideElement = this.addPlaceholderBesideElement.bind(this);
        this.addPlaceholderInZone = this.addPlaceholderInZone.bind(this);
        this.removePlaceholders = this.removePlaceholders.bind(this);
        this.removePlaceholdersAfterTimeout = this.removePlaceholdersAfterTimeout.bind(this);
    }

    /**
     * Finds next element identifier
     *
     * @method findNextElementIdentifier
     * @param {HTMLElement} placeholder
     * @param {String} elementIdentifier
     * @returns {String|null}
     */
    findNextElementIdentifier(placeholder, elementIdentifier) {
        if (placeholder.dataset.position === PLACEHOLDER_POSITION_TOP) {
            return placeholder.dataset[elementIdentifier];
        }

        return placeholder.nextElementSibling ? placeholder.nextElementSibling.dataset[elementIdentifier] : null;
    }

    /**
     * Removes placeholders
     *
     * @method removePlaceholders
     * @param {NodeList|Array} placeholders
     * @param {Function} removePlaceholderCallback
     */
    removePlaceholders(placeholders, removePlaceholderCallback) {
        placeholders.forEach(removePlaceholderCallback);
    }

    /**
     * Removes a placeholder node without removal animation
     *
     * @method removePlaceholderWithoutAnimation
     * @param {HTMLElement} placeholder
     */
    removePlaceholderWithoutAnimation(placeholder) {
        window.clearTimeout(this.placeholderRemovalTimeout);
        window.clearTimeout(this.onDragOverTimeout);

        if (!placeholder || !placeholder.parentNode) {
            return;
        }

        placeholder.parentNode.removeChild(placeholder);
    }

    /**
     * Removes a placeholder with removal animation
     *
     * @method removePlaceholderWithAnimation
     * @param {HTMLElement} placeholder
     */
    removePlaceholderWithAnimation(placeholder) {
        placeholder.classList.add(CLASS_PLACEHOLDER_REMOVING);

        window.clearTimeout(this.placeholderRemovalTimeout);
        window.clearTimeout(this.onDragOverTimeout);

        this.placeholderRemovalTimeout = window.setTimeout(
            () => this.removePlaceholderWithoutAnimation(placeholder),
            TIMEOUT_REMOVE_PLACEHOLDERS,
        );
    }

    /**
     * Rremoves a placeholder node after a timeout
     *
     * @method removePlaceholdersAfterTimeout
     * @param {Function} onTimeout
     */
    removePlaceholdersAfterTimeout(getPlaceholderNodes, onTimeout) {
        window.clearTimeout(this.onDragOverTimeout);

        this.onDragOverTimeout = window.setTimeout(() => {
            this.removePlaceholders(getPlaceholderNodes(), this.removePlaceholderWithAnimation);
            onTimeout();
        }, TIMEOUT_REMOVE_PLACEHOLDERS);
    }

    /**
     * Adds a placeholder node beside element (above or below)
     *
     * @method addPlaceholderBesideElement
     * @param {HTMLElement} element
     * @param {Number} positionY
     * @param {NodeList|Array} placeholders
     * @param {String} elementIdentifier a data attribute identifier (like 'zone' stays for `data-zone`)
     * @param {Function} onElementDragOver
     * @returns {Object}
     */
    addPlaceholderBesideElement(
        element,
        positionY,
        placeholders,
        elementIdentifier,
        onElementDragOver,
        { placeholderPosition, placeholderElementId, placeholderZoneId },
    ) {
        const placeholder = this.createPlaceholder(element, elementIdentifier, onElementDragOver);
        const rect = element.getBoundingClientRect();
        const middlePositionY = rect.top + rect.height / 2;
        const position = positionY <= middlePositionY ? PLACEHOLDER_POSITION_TOP : 'bottom';
        const placehoderExists =
            placeholderPosition === position &&
            placeholderElementId === placeholder.dataset[elementIdentifier] &&
            placeholderZoneId === placeholder.dataset.ibexaZoneId;

        if (placehoderExists) {
            return { placeholderPosition, placeholderElementId, placeholderZoneId };
        }

        this.removePlaceholders(placeholders, this.removePlaceholderWithoutAnimation);

        placeholder.dataset.position = position;

        if (position === PLACEHOLDER_POSITION_TOP) {
            this.insertBefore(element, placeholder);
        } else {
            this.insertAfter(element, placeholder);
        }

        return {
            placeholderElementId: placeholder.dataset[elementIdentifier],
            placeholderZoneId: placeholder.dataset.ibexaZoneId,
            placeholderPosition: position,
        };
    }

    /**
     * Add a placeholder node inside a zone
     *
     * @method addPlaceholderInZone
     * @param {HTMLElement} zone
     * @param {NodeList|Array} placeholders
     * @param {String} elementIdentifier a data attribute identifier (like 'zone' stays for `data-zone`)
     * @param {Function} onElementDragOver
     */
    addPlaceholderInZone(zone, placeholders, elementIdentifier, onElementDragOver) {
        const placeholder = this.createPlaceholder(zone, elementIdentifier, onElementDragOver);

        this.removePlaceholders(placeholders, this.removePlaceholderWithoutAnimation);

        zone.appendChild(placeholder);
    }

    /**
     * Inserts a node before a target node
     *
     * @method insertBefore
     * @param {HTMLElement} where
     * @param {HTMLElement} what
     */
    insertBefore(where, what) {
        if (where && where.parentNode) {
            where.parentNode.insertBefore(what, where);
        }
    }

    /**
     * Inserts a node after a target node
     *
     * @method insertAfter
     * @param {HTMLElement} where
     * @param {HTMLElement} what
     */
    insertAfter(where, what) {
        if (where && where.parentNode) {
            where.parentNode.insertBefore(what, where.nextSibling);
        }
    }

    /**
     * Create a placeholder node
     *
     * @method createPlaceholder
     * @param {HTMLElement} element
     * @param {String} elementIdentifier a data attribute identifier (like 'zone' stays for `data-zone`)
     * @returns {HTMLElement}
     */
    createPlaceholder(element, elementIdentifier) {
        const placeholder = document.createElement('div');

        placeholder.classList.add(CLASS_PLACEHOLDER);

        if (element.dataset[elementIdentifier]) {
            placeholder.dataset[elementIdentifier] = element.dataset[elementIdentifier];
        }

        placeholder.dataset.zoneId = element.dataset.zoneId ? element.dataset.zoneId : element.closest(SELECTOR_ZONE).dataset.ibexaZoneId;

        return placeholder;
    }

    /**
     * Scroll container
     *
     * @method scrollContainer
     * @param {Event} event
     * @param {Number} event.clientY
     * @param {HTMLElement} container
     */
    scrollContainer({ clientY }, container) {
        const diffSize = 50;

        if (container.innerHeight - clientY < diffSize) {
            container.scrollBy({ top: 5 });
        } else if (clientY < diffSize) {
            container.scrollBy({ top: -5 });
        }
    }

    /**
     * Gets an element recursively that matches a callback action comparison
     *
     * @method getElement
     * @param {HTMLElement} element
     * @param {Function} checkIsCorrect comparison check callback
     * @returns {HTMLElement|undefined}
     */
    getElement(element, checkIsCorrect) {
        const parent = element.parentNode;

        if (!parent) {
            return undefined;
        }

        if (checkIsCorrect(element)) {
            return element;
        }

        return checkIsCorrect(parent) ? parent : this.getElement(parent, checkIsCorrect);
    }

    /**
     * Returns initial drag & drop state
     *
     * @method getInitDragDropState
     * @returns {Object}
     */
    getInitDragDropState() {
        return {
            draggedSidebarElementType: null,
            dragOverZoneId: null,
            placeholderType: null,
            placeholderElementId: null,
            placeholderZoneId: null,
            placeholderPosition: null,
        };
    }

    render() {
        return this.props.render({
            getElement: this.getElement,
            getInitDragDropState: this.getInitDragDropState,
            scrollContainer: this.scrollContainer,
            removePlaceholderWithAnimation: this.removePlaceholderWithAnimation,
            removePlaceholderWithoutAnimation: this.removePlaceholderWithoutAnimation,
            insertAfter: this.insertAfter,
            insertBefore: this.insertBefore,
            removePlaceholders: this.removePlaceholders,
            addPlaceholderBesideElement: this.addPlaceholderBesideElement,
            addPlaceholderInZone: this.addPlaceholderInZone,
            removePlaceholdersAfterTimeout: this.removePlaceholdersAfterTimeout,
            findNextElementIdentifier: this.findNextElementIdentifier,
        });
    }
}

DragDrop.propTypes = {
    render: PropTypes.func.isRequired,
};

export default DragDrop;
