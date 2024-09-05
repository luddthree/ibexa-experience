<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder;

use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Forwards all main request query parameters to block-rendering sub-request.
 *
 * @internal
 */
final class BlockQueryParametersSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    /** @var array<string> */
    private array $blockIdentifierList;

    /**
     * @param array<string> $blockIdentifierList
     */
    public function __construct(RequestStack $requestStack, array $blockIdentifierList)
    {
        $this->requestStack = $requestStack;
        $this->blockIdentifierList = $blockIdentifierList;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::GLOBAL_BLOCK_RENDER_PRE => [
                ['onBlockPreRender', -100],
            ],
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        if (!in_array($event->getBlockValue()->getType(), $this->blockIdentifierList, true)) {
            return;
        }

        // forward query parameters (needed for e.g.: pagination) from the main request to block-rendering sub-request
        $mainRequest = $this->requestStack->getMainRequest();
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (null !== $mainRequest && null !== $currentRequest && $mainRequest !== $currentRequest) {
            $this->forwardQueryParameters($mainRequest, $currentRequest);
        }
    }

    private function forwardQueryParameters(Request $fromRequest, Request $toRequest): void
    {
        $toRequest->query->add($fromRequest->query->all());
    }
}
