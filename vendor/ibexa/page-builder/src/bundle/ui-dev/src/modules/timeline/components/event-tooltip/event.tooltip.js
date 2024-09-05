import React, { PureComponent } from 'react';
import { createPortal } from 'react-dom';
import PropTypes from 'prop-types';

const { Popper } = window;

class EventTooltip extends PureComponent {
    constructor(props) {
        super(props);

        this.eventsTooltipsContainer = document.querySelector('.ibexa-pb-events-tooltip-container');
        this.popperInstance = null;

        this.renderDescription = this.renderDescription.bind(this);
    }

    componentDidMount() {
        this.createPopperInstanceOrUpdate();
    }

    componentDidUpdate() {
        this.createPopperInstanceOrUpdate();
    }

    createPopperInstanceOrUpdate() {
        const { markerRef, innerRef } = this.props;
        const markerNode = markerRef.current;
        const tooltipNode = innerRef.current;

        if (!markerNode) {
            return;
        }

        if (!this.popperInstance) {
            this.popperInstance = Popper.createPopper(markerNode, tooltipNode, {
                placement: 'left',
                modifiers: [
                    {
                        name: 'offset',
                        options: {
                            offset: ({ placement, popper }) => {
                                if (placement === 'left' || placement === 'right') {
                                    return [popper.height / 2 - 30, 8];
                                }
                                return [0, 8];
                            },
                        },
                    },
                    {
                        name: 'flip',
                        options: {
                            fallbackPlacements: ['right', 'bottom', 'top'],
                        },
                    },
                ],
            });
        } else {
            this.popperInstance.update();
        }
    }

    setInnerHTML(__html) {
        return { __html };
    }

    renderDescription(description, index) {
        return <div key={index} className="c-pb-event-tooltip__item" dangerouslySetInnerHTML={this.setInnerHTML(description)} />;
    }

    render() {
        const { descriptions, innerRef } = this.props;

        return createPortal(
            <div className="c-pb-event-tooltip" ref={innerRef}>
                <div className="c-pb-event-tooltip__arrow-wrapper" data-popper-arrow={true}>
                    <div className="c-pb-event-tooltip__arrow" data-popper-arrow={true} />
                </div>
                <div className="c-pb-event-tooltip__hidden-wrapper">
                    <div className="c-pb-event-tooltip__scroll-wrapper">{descriptions.map(this.renderDescription)}</div>
                </div>
            </div>,
            this.eventsTooltipsContainer,
        );
    }
}

EventTooltip.propTypes = {
    descriptions: PropTypes.string.isRequired,
    markerRef: PropTypes.shape({ current: PropTypes.instanceOf(Element) }),
    innerRef: PropTypes.shape({ current: PropTypes.instanceOf(Element) }),
};

EventTooltip.defaultProps = {
    markerRef: {},
    innerRef: {},
};

const EventTooltipForwarded = React.forwardRef((props, ref) => <EventTooltip innerRef={ref} {...props} />);

EventTooltipForwarded.displayName = 'EventTooltip';

export default EventTooltipForwarded;
