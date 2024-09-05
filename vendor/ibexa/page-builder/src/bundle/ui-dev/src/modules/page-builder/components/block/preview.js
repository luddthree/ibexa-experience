import React, { forwardRef, useState } from 'react';
import PropTypes from 'prop-types';

const { Translator } = window;

const CLASS_INVISIBLE = 'ibexa-mark-invisible';
const setInnerHTML = (__html) => ({ __html });
const setInvisibleClass = (preview, isVisible) => {
    const container = document.createElement('div');

    container.insertAdjacentHTML('beforeend', preview);

    [...container.childNodes].forEach((child) => {
        if (child instanceof HTMLElement) {
            child.classList.toggle(CLASS_INVISIBLE, !isVisible);
        }
    });

    return container.innerHTML;
};
const Preview = forwardRef(
    (
        {
            hasInsertAnimation: hasInsertAnimationProp,
            preview: previewProp,
            isValid,
            shouldDisplayError,
            isAvailable,
            removeBlock,
            label,
            isVisible,
            isConfigPanelOpenedOnInit,
        },
        ref,
    ) => {
        const [hasInsertAnimation, setHasInsertAnimation] = useState(hasInsertAnimationProp);
        const baseClassName = 'c-pb-block-preview__inner';
        const onRef = (node) => {
            node?.addEventListener('animationend', () => {
                if (hasInsertAnimation) {
                    setHasInsertAnimation(false);
                }
            });

            ref.current = node;
        };
        let className = `${baseClassName}`;
        let preview = previewProp.trim();

        if (hasInsertAnimation) {
            className = `${className} ${baseClassName}--has-insert-animation`;
        }

        if (!isValid && shouldDisplayError && !isConfigPanelOpenedOnInit) {
            className = `${className} ${baseClassName}--invalid`;
        }

        if (!isAvailable) {
            className = `${className} ${baseClassName}--unavailable`;
            preview = setInvisibleClass(preview, isVisible);

            return (
                <div className={className}>
                    <div className="c-pb-block-preview__unavailable-mask">
                        <div className="c-pb-block-preview__unavailable-mask-title">
                            {Translator.trans(
                                /*@Desc("This element is not available in this page")*/ 'block.no_availability.title',
                                {},
                                'ibexa_page_builder',
                            )}
                        </div>
                        <div className="c-pb-block-preview__unavailable-mask-content">
                            {Translator.trans(
                                /*@Desc("You have to delete it to publish")*/ 'block.no_availability.content',
                                {},
                                'ibexa_page_builder',
                            )}
                        </div>
                        <a className="c-pb-block-preview__delete-unavailable-block" onClick={removeBlock}>
                            {Translator.trans(/*@Desc("Delete")*/ 'block.no_availability.delete', {}, 'ibexa_page_builder')}
                        </a>
                    </div>
                    <div
                        className="c-pb-block-preview__unavailable-block-content"
                        ref={ref}
                        dangerouslySetInnerHTML={setInnerHTML(preview)}
                    />
                </div>
            );
        }

        if (preview.length) {
            preview = setInvisibleClass(preview, isVisible);

            return <div className={className} ref={onRef} dangerouslySetInnerHTML={setInnerHTML(preview)} />;
        }

        className = `${className} ${baseClassName}--empty`;

        return (
            <div className={className} ref={onRef}>
                {label}
            </div>
        );
    },
);

Preview.displayName = 'Preview';

Preview.propTypes = {
    preview: PropTypes.string.isRequired,
    label: PropTypes.node.isRequired,
    shouldDisplayError: PropTypes.bool.isRequired,
    isValid: PropTypes.bool.isRequired,
    isVisible: PropTypes.bool.isRequired,
    isAvailable: PropTypes.bool,
    removeBlock: PropTypes.func,
    hasInsertAnimation: PropTypes.bool,
    isConfigPanelOpenedOnInit: PropTypes.bool,
};

Preview.defaultProps = {
    isConfigPanelOpenedOnInit: false,
    isAvailable: false,
    removeBlock: () => {},
    hasInsertAnimation: false,
};

export default Preview;
