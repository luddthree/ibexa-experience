import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import PropTypes from 'prop-types';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';
import { createCssClassNames } from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/helpers/css.class.names';
import SidebarBlock from '../block/sidebar.block';
import SidebarBlocksGroup from '../block/sidebar.blocks.group';

const { Translator } = window;

const CLASS_IFRAME_BACKDROP_DISPLAYED = 'ibexa-pb-app--iframe-backdrop-visible';
const MENU_BAR_HEIGHT = 68;

/**
 * @deprecated This component is deprecated and will be removed in 5.0; Use toolbox.js
 */
class Sidebar extends Component {
    constructor(props) {
        super(props);

        this.infobarTogglerContainer = document.querySelector('.ibexa-pb-action-bar__right');

        this._refWrapper = null;
        this._refDragHandler = null;
        this.updateViewportBoundaries = this.updateViewportBoundaries.bind(this);
        this.toggleCollapsedState = this.toggleCollapsedState.bind(this);
        this.handleDragStart = this.handleDragStart.bind(this);
        this.handleDragEnd = this.handleDragEnd.bind(this);
        this.handleDrag = this.handleDrag.bind(this);
        this.setWrapperRef = this.setWrapperRef.bind(this);
        this.setDragHandlerRef = this.setDragHandlerRef.bind(this);
        this.updateBlockFilterQuery = this.updateBlockFilterQuery.bind(this);
        this.updatePositionAndDimensions = this.updatePositionAndDimensions.bind(this);

        this.state = {
            isCollapsed: props.isCollapsed,
            isDragged: false,
            isDragging: false,
            top: 0,
            left: 0,
            sidebarWidth: 0,
            sidebarHeight: 0,
            windowWidth: 0,
            windowHeight: 0,
            diffX: 0,
            diffY: 0,
            blockFilterQuery: '',
        };
    }

    componentDidMount() {
        const rect = this._refWrapper.getBoundingClientRect();

        this.setState((state) => ({
            ...state,
            windowWidth: window.innerWidth,
            windowHeight: window.innerHeight,
            sidebarWidth: rect.width,
            sidebarHeight: rect.height,
        }));

        window.addEventListener('resize', this.updateViewportBoundaries, false);
        document.body.addEventListener('mouseup', this.handleDragEnd, false);
    }

    componentDidUpdate(prevProps, prevState) {
        const shouldUpdatePositionAndDimension =
            this.state.isCollapsed !== prevState.isCollapsed ||
            this.state.blockFilterQuery !== prevState.blockFilterQuery ||
            this.props.blocksToRender !== prevProps.blocksToRender;

        if (shouldUpdatePositionAndDimension) {
            this.updatePositionAndDimensions();
        }
    }

    componentWillUnmount() {
        window.removeEventListener('resize', this.updateViewportBoundaries);
        document.body.removeEventListener('mouseup', this.handleDragEnd);
    }

    updateViewportBoundaries() {
        this.setState((state) => ({
            ...state,
            sidebarHeight: this._refWrapper.offsetHeight,
            windowWidth: window.innerWidth,
            windowHeight: window.innerHeight,
        }));
    }

    updatePositionAndDimensions() {
        const rect = this._refWrapper.getBoundingClientRect();
        const { top, left } = this.getPosition({
            clientX: rect.left + this.state.diffX,
            clientY: rect.top + this.state.diffY,
            sidebarHeight: rect.height,
        });

        this.setState((state) => ({ ...state, sidebarHeight: this._refWrapper.offsetHeight, top, left }));
    }

    updateBlockFilterQuery(event) {
        this.setState({ blockFilterQuery: event.target.value });
    }

    toggleCollapsedState() {
        this.setState((state) => ({ ...state, isCollapsed: !state.isCollapsed }));
    }

    getPosition({ clientX, clientY, sidebarHeight = this.state.sidebarHeight, diffX = this.state.diffX, diffY = this.state.diffY }) {
        const { windowHeight, windowWidth, sidebarWidth } = this.state;
        const maxOffsetLeft = windowWidth - sidebarWidth;
        const maxOffsetTop = windowHeight - sidebarHeight;

        clientX = clientX - diffX;
        clientY = clientY - diffY;

        return {
            top: clientY < maxOffsetTop ? Math.max(MENU_BAR_HEIGHT, clientY) : maxOffsetTop,
            left: clientX < maxOffsetLeft ? Math.max(0, clientX) : maxOffsetLeft,
        };
    }

    handleDragStart(event) {
        const isSidebarToggler =
            event.nativeEvent.target.classList.contains('c-pb-sidebar__toggler') ||
            !!event.nativeEvent.target.closest('.c-pb-sidebar__toggler');
        const buttonPressedCode = event.buttons !== undefined ? event.buttons : event.nativeEvent.which;
        const isLeftClick = buttonPressedCode === 1;

        if (isSidebarToggler || !isLeftClick) {
            return;
        }

        const rect = this._refDragHandler.getBoundingClientRect();
        const diffX = event.nativeEvent.clientX - rect.left;
        const diffY = event.nativeEvent.clientY - rect.top;
        const { top, left } = this.getPosition({ ...event, diffX, diffY });

        this.setState(
            (state) => ({ ...state, isDragged: true, isDragging: true, top, left, diffX, diffY }),
            () => {
                document.body.classList.add(CLASS_IFRAME_BACKDROP_DISPLAYED);
                document.body.addEventListener('mousemove', this.handleDrag, false);
                document.body.addEventListener('mouseleave', this.handleDragEnd, false);
            },
        );
    }

    handleDragEnd(event) {
        if (!this.state.isDragging) {
            return;
        }

        const { top, left } = this.getPosition(event);

        this.setState(
            (state) => ({ ...state, isDragged: true, isDragging: false, top, left, diffX: 0, diffY: 0 }),
            () => {
                document.body.classList.remove(CLASS_IFRAME_BACKDROP_DISPLAYED);
                document.body.removeEventListener('mousemove', this.handleDrag);
                document.body.removeEventListener('mouseleave', this.handleDragEnd);
            },
        );
    }

    handleDrag(event) {
        event.preventDefault();

        const { top, left } = this.getPosition(event);

        this.setState((state) => ({ ...state, isDragged: true, isDragging: true, top, left }));
    }

    setWrapperRef(ref) {
        this._refWrapper = ref;
    }

    setDragHandlerRef(ref) {
        this._refDragHandler = ref;
    }

    renderInfobarCollapseBtn() {
        const btnClassName = createCssClassNames({
            'btn ibexa-btn ibexa-btn--tertiary ibexa-btn--no-text c-pb-sidebar__infobar-toggler': true,
            'c-pb-sidebar__infobar-toggler--collapsed': this.state.isCollapsed,
        });
        const title = Translator.trans(/*@Desc("Toggle Elements")*/ 'sidebar.toggle.label', {}, 'page_builder');

        return (
            <button className={btnClassName} title={title} onClick={this.toggleCollapsedState} type="button">
                <Icon name="collapse" extraClasses="ibexa-icon--small" />
            </button>
        );
    }

    renderSidebarBlocks() {
        const { blocksToRender, onBlockDrag, onBlockDragStart, isAddingBlocksEnabled } = this.props;
        const { blockFilterQuery } = this.state;
        const blockFilterQueryLowerCase = blockFilterQuery.toLowerCase();
        const blocksByCategories = blocksToRender.reduce((output, config) => {
            const blockNameLowerCase = config.name.toLowerCase();
            const isHidden = !blockNameLowerCase.includes(blockFilterQueryLowerCase);

            if (!(config.category in output)) {
                output[config.category] = [];
            }

            output[config.category].push(
                <SidebarBlock
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

        const categoriesSorted = this.sortBlockCategories(blocksByCategories);

        return categoriesSorted.map((blockCategory) => (
            <SidebarBlocksGroup
                title={blockCategory}
                key={`block-group-${blockCategory}`}
                onCollapseChange={this.updatePositionAndDimensions}
            >
                {blocksByCategories[blockCategory]}
            </SidebarBlocksGroup>
        ));
    }

    sortBlockCategories(categories) {
        const sortList = ['default', 'PIM', 'Customer Portal', 'Commerce'];

        return Object.keys(categories).sort((a, b) => {
            const indexA = sortList.indexOf(a);
            const indexB = sortList.indexOf(b);

            if (indexA === -1 || indexB === -1) {
                return 1;
            }

            return indexA - indexB;
        });
    }

    render() {
        const { isCollapsed, isDragged, top, left } = this.state;
        const wrapperAttrs = {
            ref: this.setWrapperRef,
            className: createCssClassNames({
                'c-pb-sidebar': true,
                'c-pb-sidebar--draggable': true,
                'c-pb-sidebar--collapsed': isCollapsed,
            }),
        };
        const title = Translator.trans(/*@Desc("Elements")*/ 'sidebar.title', {}, 'ibexa_page_builder');
        const blockFilterPlaceholder = Translator.trans(/*@Desc("Search...")*/ 'block_filter.placeholder', {}, 'ibexa_page_builder');

        if (isDragged) {
            wrapperAttrs.style = {
                top: `${top}px`,
                left: `${left}px`,
                position: 'fixed',
            };
        }

        return (
            <>
                {ReactDOM.createPortal(this.renderInfobarCollapseBtn(), this.infobarTogglerContainer)}
                <div {...wrapperAttrs}>
                    <div className="c-pb-sidebar__action-bar" onMouseDown={this.handleDragStart} ref={this.setDragHandlerRef}>
                        <div className="c-pb-sidebar__icon-wrapper">
                            <Icon name="drag" extraClasses="ibexa-icon--tiny-small" />
                        </div>
                        <div className="c-pb-sidebar__toggler" onClick={this.toggleCollapsedState}>
                            <Icon name="box-collapse" extraClasses="ibexa-icon--small" />
                        </div>
                    </div>
                    <div className="c-pb-sidebar__title-bar">
                        <h3 className="c-pb-sidebar__title">{title}</h3>
                    </div>
                    <div className="c-pb-sidebar__search-bar">
                        <input
                            type="text"
                            name="filter"
                            placeholder={blockFilterPlaceholder}
                            value={this.state.blockFilterQuery}
                            onChange={this.updateBlockFilterQuery}
                            className="form-control ibexa-input ibexa-input--text c-pb-sidebar__sidebar-filter"
                        />
                    </div>
                    <div className="c-pb-sidebar__list">{this.renderSidebarBlocks()}</div>
                </div>
            </>
        );
    }
}

Sidebar.propTypes = {
    blocksToRender: PropTypes.array.isRequired,
    onBlockDrag: PropTypes.func.isRequired,
    onBlockDragStart: PropTypes.func.isRequired,
    isAddingBlocksEnabled: PropTypes.bool.isRequired,
    isCollapsed: PropTypes.bool,
};

Sidebar.defaultProps = {
    isCollapsed: false,
};

export default Sidebar;
