<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use DateTimeImmutable;
use Ibexa\Contracts\AdminUi\Resolver\IconPathResolverInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Contracts\PageBuilder\Timeline\EventInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\PageBuilder\PageBuilder\Timeline\BasicEvent;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageContextInterface;
use Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent;
use Ibexa\PageBuilder\PageBuilder\Timeline\Event\TimelineEvents;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class TimelineEventsSubscriber implements EventSubscriberInterface
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Twig\Environment */
    private $templating;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /** @var \Ibexa\Contracts\AdminUi\Resolver\IconPathResolverInterface */
    private $iconPathResolver;

    public function __construct(
        TranslatorInterface $translator,
        Environment $templating,
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        IconPathResolverInterface $iconPathResolver
    ) {
        $this->translator = $translator;
        $this->templating = $templating;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
        $this->iconPathResolver = $iconPathResolver;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TimelineEvents::COLLECT_EVENTS => [
                ['addBlockVisibilityEvents', 0],
                ['filterOutPastEvents', -128],
            ],
        ];
    }

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent $event
     */
    public function addBlockVisibilityEvents(ContentTimelineEvent $event): void
    {
        $context = $event->getContext();

        if (!$context instanceof PageContextInterface) {
            return;
        }

        $events = [$event->getTimelineEvents()];
        $page = $context->getPage();

        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                $events[] = $this->getBlockEvents($block);
            }
        }

        $event->setTimelineEvents(array_merge(...$events));
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     *
     * @return \Ibexa\Contracts\PageBuilder\Timeline\EventInterface[]
     */
    private function getBlockEvents(BlockValue $blockValue): array
    {
        $blockEvents = [];
        $revealDate = $blockValue->getSince();
        $hideDate = $blockValue->getTill();
        $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($blockValue->getType());

        if (null !== $revealDate) {
            $blockEvents[] = $this->createRevealBlockEvent($blockValue, $blockDefinition, $revealDate);
        }

        if (null !== $hideDate) {
            $blockEvents[] = $this->createHideBlockEvent($blockValue, $blockDefinition, $hideDate);
        }

        return $blockEvents;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     * @param \DateTimeInterface $date
     *
     * @return \Ibexa\Contracts\PageBuilder\Timeline\EventInterface
     */
    private function createRevealBlockEvent(
        BlockValue $blockValue,
        BlockDefinition $blockDefinition,
        \DateTimeInterface $date
    ): EventInterface {
        $eventName = /** @Desc("Reveal block") */
            $this->translator->trans('event.reveal_block.title', [], 'ibexa_page_builder_timeline_events');

        return new BasicEvent(
            $eventName,
            $this->templating->render(
                '@IbexaPageBuilder/page_builder/timeline/events/block_visibility_event_description.twig',
                [
                    'name' => $eventName,
                    'date' => $date,
                    'block_value' => $blockValue,
                    'block_definition' => $blockDefinition,
                ]
            ),
            $date,
            $this->iconPathResolver->resolve('block-visible')
        );
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     * @param \DateTimeInterface $date
     *
     * @return \Ibexa\Contracts\PageBuilder\Timeline\EventInterface
     */
    private function createHideBlockEvent(
        BlockValue $blockValue,
        BlockDefinition $blockDefinition,
        \DateTimeInterface $date
    ): EventInterface {
        $eventName = /** @Desc("Hide block") */
            $this->translator->trans('event.hide_block.title', [], 'ibexa_page_builder_timeline_events');

        return new BasicEvent(
            $eventName,
            $this->templating->render(
                '@IbexaPageBuilder/page_builder/timeline/events/block_visibility_event_description.twig',
                [
                    'name' => $eventName,
                    'date' => $date,
                    'block_value' => $blockValue,
                    'block_definition' => $blockDefinition,
                ]
            ),
            $date,
            $this->iconPathResolver->resolve('block-invisible')
        );
    }

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent $event
     *
     * @throws \Exception
     */
    public function filterOutPastEvents(ContentTimelineEvent $event): void
    {
        $now = new DateTimeImmutable();
        $events = array_filter($event->getTimelineEvents(), static function (EventInterface $event) use ($now) {
            return $event->getDate() >= $now;
        });

        $event->setTimelineEvents($events);
    }
}

class_alias(TimelineEventsSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\TimelineEventsSubscriber');
