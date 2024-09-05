<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use Ibexa\FieldTypeRichText\RichText\DOMDocumentFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RichTextBlockSubscriber implements EventSubscriberInterface
{
    private const EMPTY_RICHTEXT = '<?xml version="1.0" encoding="UTF-8"?><section/>';

    /** @var \Ibexa\FieldTypeRichText\RichText\DOMDocumentFactory */
    private $domDocumentFactory;

    /**
     * @param \Ibexa\FieldTypeRichText\RichText\DOMDocumentFactory $domDocumentFactory
     */
    public function __construct(DOMDocumentFactory $domDocumentFactory)
    {
        $this->domDocumentFactory = $domDocumentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName('richtext') => 'onBlockPreRender',
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     */
    public function onBlockPreRender(PreRenderEvent $event): void
    {
        $renderRequest = $event->getRenderRequest();

        if (!$renderRequest instanceof TwigRenderRequest) {
            return;
        }

        $xml = $event->getBlockValue()->getAttribute('content')->getValue();

        $parameters = $renderRequest->getParameters();
        $parameters['document'] = $this->domDocumentFactory->loadXMLString($xml ?? self::EMPTY_RICHTEXT);

        $renderRequest->setParameters($parameters);
    }
}

class_alias(RichTextBlockSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\RichTextBlockSubscriber');
