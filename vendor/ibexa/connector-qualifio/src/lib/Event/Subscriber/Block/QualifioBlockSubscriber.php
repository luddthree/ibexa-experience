<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Event\Subscriber\Block;

use Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class QualifioBlockSubscriber implements EventSubscriberInterface
{
    private const BLOCK_IDENTIFIER = 'qualifio';

    private QualifioServiceInterface $qualifioService;

    public function __construct(
        QualifioServiceInterface $qualifioService
    ) {
        $this->qualifioService = $qualifioService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::BLOCK_IDENTIFIER) => 'onBlockPreRender',
            BlockDefinitionEvents::getBlockDefinitionEventName(self::BLOCK_IDENTIFIER) => 'onBlockDefinition',
        ];
    }

    public function onBlockDefinition(BlockDefinitionEvent $event): void
    {
        if (!$this->qualifioService->isConfigured()) {
            $event->getDefinition()->setVisible(false);
        }
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        $renderRequest = $event->getRenderRequest();
        if (!$renderRequest instanceof TwigRenderRequest) {
            throw new LogicException(sprintf(
                'Expected an instance of %s, received %s.',
                TwigRenderRequest::class,
                get_debug_type($renderRequest),
            ));
        }

        $parameters = $renderRequest->getParameters();
        if (!array_key_exists('campaign', $parameters)) {
            return;
        }

        $campaignId = (int)$parameters['campaign'];
        if ($campaignUrl = $this->qualifioService->buildCampaignUrl($campaignId)) {
            $parameters['url'] = $campaignUrl;
        }

        $renderRequest->setParameters($parameters);
    }
}
