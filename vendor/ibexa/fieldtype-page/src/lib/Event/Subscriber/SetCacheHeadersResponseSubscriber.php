<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\FieldTypePage\Event\BlockResponseEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\ContentViewBlockContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SetCacheHeadersResponseSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var string */
    private $userHashHeaderName;

    public function __construct(
        ConfigResolverInterface $configResolver,
        string $userHashHeaderName
    ) {
        $this->configResolver = $configResolver;
        $this->userHashHeaderName = $userHashHeaderName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockResponseEvents::BLOCK_RESPONSE => ['onBlockResponse', 0],
        ];
    }

    public function onBlockResponse(BlockResponseEvent $event): void
    {
        $blockContext = $event->getBlockContext();
        $response = $event->getResponse();

        if (!$blockContext instanceof ContentViewBlockContext) {
            return;
        }

        if (!$this->configResolver->getParameter('content.ttl_cache')) {
            $response->setPrivate();

            return;
        }

        $response->setPublic();
        $response->setSharedMaxAge((int) $this->configResolver->getParameter('content.default_ttl'));
        $response->setVary([$this->userHashHeaderName, 'X-Editorial-Mode']);
    }
}

class_alias(SetCacheHeadersResponseSubscriber::class, 'EzSystems\EzPlatformPageFieldType\Event\Subscriber\SetCacheHeadersResponseSubscriber');
