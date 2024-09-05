import React, { useEffect, useRef, useContext } from 'react';
import { createPortal } from 'react-dom';
import PropTypes from 'prop-types';

import { EventsTooltipRefContext } from '../../../calendar.module';
import Icon from '@ibexa-admin-ui/src/bundle/ui-dev/src/modules/common/icon/icon';

const { Popper } = window;

const EventsListTooltip = ({ title, children, closeFunc, refEvents, innerRef }) => {
    const popperInstanceRef = useRef(null);
    const eventsTooltipContainerRef = useContext(EventsTooltipRefContext);
    const createPopperInstanceOrUpdate = () => {
        const eventsListNode = refEvents.current;
        const tooltipNode = innerRef.current;

        if (!eventsListNode) {
            return;
        }

        if (!popperInstanceRef.current) {
            const newPoperInstance = Popper.createPopper(eventsListNode, tooltipNode, {
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

            popperInstanceRef.current = newPoperInstance;
        } else {
            popperInstanceRef.current.update();
        }
    };

    useEffect(() => {
        createPopperInstanceOrUpdate();
    }, [refEvents.current]);

    return createPortal(
        <div className="c-events-list-tooltip" ref={innerRef}>
            <div className="c-events-list-tooltip__header">
                <div className="c-events-list-tooltip__title">{title}</div>
                <button className="btn ibexa-btn ibexa-btn--small ibexa-btn--no-text" onClick={closeFunc} type="button">
                    <Icon name="discard" extraClasses="ibexa-icon--tiny-small" />
                </button>
            </div>
            <div className="c-events-list-tooltip__body">{children}</div>
        </div>,
        eventsTooltipContainerRef.current,
    );
};

EventsListTooltip.propTypes = {
    children: PropTypes.element,
    refEvents: PropTypes.shape({ current: PropTypes.instanceOf(Element) }),
    innerRef: PropTypes.shape({ current: PropTypes.instanceOf(Element) }),
};

const EventsListTooltipForwarded = React.forwardRef((props, ref) => <EventsListTooltip innerRef={ref} {...props} />);

EventsListTooltipForwarded.displayName = 'EventsListTooltip';

export default EventsListTooltipForwarded;
