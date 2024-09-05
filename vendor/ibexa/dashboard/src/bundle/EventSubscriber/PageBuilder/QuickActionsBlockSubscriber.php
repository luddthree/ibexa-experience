<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Dashboard\Block\QuickActions\ConfigurationProviderInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class QuickActionsBlockSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const BLOCK_IDENTIFIER = 'quick_actions';

    private ConfigurationProviderInterface $configurationProvider;

    public function __construct(ConfigurationProviderInterface $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
        $this->logger = new NullLogger();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::BLOCK_IDENTIFIER) => [
                ['onBlockPreRender', -100],
            ],
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $request */
        $request = $event->getRenderRequest();
        $blockValue = $event->getBlockValue();
        $actionsAttribute = $blockValue->getAttribute('actions');
        // value format from block select type configuration: create_content,create_product
        $actions = null !== $actionsAttribute ? explode(',', $actionsAttribute->getValue()) : [];
        $tiles = [];
        foreach ($actions as $action) {
            try {
                $tiles[] = $this->configurationProvider->getConfiguration($action);
            } catch (InvalidArgumentException $e) {
                $this->logger->warning(
                    sprintf(
                        'Failed to get configuration for configured quick action "%s": %s',
                        $action,
                        $e->getMessage()
                    )
                );
            }
        }
        $request->addParameter('tiles', $tiles);
    }
}
