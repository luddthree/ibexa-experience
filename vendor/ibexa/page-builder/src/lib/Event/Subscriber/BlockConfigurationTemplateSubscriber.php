<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\PageBuilder\View\BlockConfigurationView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BlockConfigurationTemplateSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    public function __construct(
        BlockDefinitionFactoryInterface $blockDefinitionFactory
    ) {
        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::PRE_CONTENT_VIEW => 'onPreContentView',
        ];
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent $event
     *
     * @throws \Exception
     */
    public function onPreContentView(PreContentViewEvent $event)
    {
        $view = $event->getContentView();

        if (!$view instanceof BlockConfigurationView) {
            return;
        }

        $blockIdentifier = $view->getBlockTypeIdentifier();
        $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockIdentifier);

        $view->setTemplateIdentifier($blockDefinition->getConfigurationTemplate());
    }
}

class_alias(BlockConfigurationTemplateSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\BlockConfigurationTemplateSubscriber');
