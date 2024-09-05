<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\EventSubscriber\PageBuilder;

use Ibexa\Bundle\Connect\Event\PrePageBlockWebhookRequestEvent;
use Ibexa\Bundle\Connect\EventSubscriber\PageBuilderPreRenderEventSubscriber;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractEnabledQueryParameterWebhookSubscriber implements EventSubscriberInterface
{
    private BlockAttributeFactoryInterface $blockAttributeFactory;

    public function __construct(
        BlockAttributeFactoryInterface $blockAttributeFactory
    ) {
        $this->blockAttributeFactory = $blockAttributeFactory;
    }

    public static function getSubscribedEvents(): array
    {
        $blockDefinitionEvent = BlockDefinitionEvents::getBlockDefinitionEventName(
            PageBuilderPreRenderEventSubscriber::IBEXA_CONNECT_BLOCK,
        );

        return [
            PrePageBlockWebhookRequestEvent::class => 'onBlockRender',
            $blockDefinitionEvent => 'onBlockDefinition',
        ];
    }

    /**
     * @phpstan-return scalar|null
     */
    abstract protected function resolveQueryParameter();

    /** @phpstan-return non-empty-string */
    abstract protected function getAttributeIdentifier(): string;

    /** @phpstan-return non-empty-string */
    abstract protected function getQueryParameterName(): string;

    abstract protected function getAttributeName(): string;

    final public function onBlockRender(PrePageBlockWebhookRequestEvent $event): void
    {
        $attributeIdentifier = $this->getAttributeIdentifier();
        $blockValue = $event->getBlockValue();
        $attribute = $blockValue->getAttribute($attributeIdentifier);
        if ($attribute === null) {
            return;
        }

        $value = $attribute->getValue();
        // $value is "0" or "1". Leaving it loosely equal for future, proper mapping.
        if (!$value) {
            return;
        }

        $parameter = $this->resolveQueryParameter();
        if ($parameter !== null) {
            $queryParameterName = $this->getQueryParameterName();
            $event->addQueryParameter($queryParameterName, $parameter);
        }
    }

    final public function onBlockDefinition(BlockDefinitionEvent $event): void
    {
        $attributeIdentifier = $this->getAttributeIdentifier();
        $configuration = $event->getConfiguration();
        $blockDefinition = $event->getDefinition();
        $attribute = $this->blockAttributeFactory->create(
            PageBuilderPreRenderEventSubscriber::IBEXA_CONNECT_BLOCK,
            $attributeIdentifier,
            [
                'name' => $this->getAttributeName(),
                'type' => 'checkbox',
                'category' => 'default',
                'validators' => [],
            ],
            $configuration,
        );

        $attributes = $blockDefinition->getAttributes();
        $attributes[$attributeIdentifier] = $attribute;
        $blockDefinition->setAttributes($attributes);
    }
}
