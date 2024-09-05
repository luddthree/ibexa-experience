<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use Ibexa\FieldTypePage\Event\BlockResponseEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvents;
use Ibexa\FieldTypePage\Event\Subscriber\BlockResponseSubscriber;
use Ibexa\FieldTypePage\FieldType\Page\Service\BlockService;
use Ibexa\PageBuilder\Block\Context\ContentCreateBlockContext;
use Ibexa\PageBuilder\Block\Context\ContentEditBlockContext;
use Ibexa\PageBuilder\Block\Context\ContentTranslateBlockContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class ContextAwareBlockResponseSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\FieldTypePage\Event\Subscriber\BlockResponseSubscriber */
    protected $blockResponseSubscriber;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService */
    private $blockService;

    /** @var \Twig\Environment */
    private $templating;

    /**
     * @param \Ibexa\FieldTypePage\Event\Subscriber\BlockResponseSubscriber
     * @param \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService $blockService
     * @param \Twig\Environment $templating
     */
    public function __construct(
        BlockResponseSubscriber $blockResponseSubscriber,
        BlockService $blockService,
        Environment $templating
    ) {
        $this->blockResponseSubscriber = $blockResponseSubscriber;
        $this->blockService = $blockService;
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            BlockResponseEvents::BLOCK_RESPONSE => ['onBlockResponse', 255],
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\BlockResponseEvent $event
     *
     * @throws \Exception
     */
    public function onBlockResponse(BlockResponseEvent $event): void
    {
        $response = $event->getResponse();

        $blockContext = $event->getBlockContext();
        $isEditorialContext = $blockContext instanceof ContentEditBlockContext
            || $blockContext instanceof ContentTranslateBlockContext
            || $blockContext instanceof ContentCreateBlockContext;

        if (!$isEditorialContext) {
            $this->blockResponseSubscriber->onBlockResponse($event);

            return;
        }

        try {
            $blockContent = $this->blockService->render($blockContext, $event->getBlockValue());
        } catch (\Exception $e) {
            $blockContent = $this->templating->render(
                '@IbexaPageBuilder/page_builder/block/error_handling/general.html.twig',
                [
                    'context' => $blockContext,
                    'block' => $event->getBlockValue(),
                ]
            );
        }

        $response->setContent($blockContent);
    }
}

class_alias(ContextAwareBlockResponseSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\ContextAwareBlockResponseSubscriber');
