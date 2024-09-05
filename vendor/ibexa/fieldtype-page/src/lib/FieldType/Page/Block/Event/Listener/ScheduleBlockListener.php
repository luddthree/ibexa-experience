<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Event\Listener;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;
use Ibexa\Core\MVC\Symfony\View\CachableView;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\FieldTypePage\Event\AttributeSerializationEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvent;
use Ibexa\FieldTypePage\Event\BlockResponseEvents;
use Ibexa\FieldTypePage\Event\PageEvents;
use Ibexa\FieldTypePage\Event\PageToPersistenceEvent;
use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter;
use Ibexa\FieldTypePage\FieldType\Page\Block\Context\ContentViewBlockContext;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator;
use Ibexa\FieldTypePage\ScheduleBlock\Item\UnavailableLocation;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleBlock;
use Ibexa\FieldTypePage\ScheduleBlock\Scheduler;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleService;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleSnapshotService;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ScheduleBlockListener implements EventSubscriberInterface
{
    /** @var \JMS\Serializer\SerializerInterface */
    private $serializer;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService */
    private $scheduleService;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator */
    private $fieldDefinitionLocator;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Scheduler */
    private $scheduler;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\ScheduleSnapshotService */
    private $scheduleSnapshotService;

    /** @var int */
    private $snapshotsAmount;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter */
    private $pageConverter;

    /**
     * @param \JMS\Serializer\SerializerInterface $serializer
     * @param \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService $scheduleService
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Ibexa\FieldTypePage\FieldType\Page\FieldDefinitionLocator $fieldDefinitionLocator
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Scheduler $scheduler
     * @param \Ibexa\FieldTypePage\ScheduleBlock\ScheduleSnapshotService $scheduleSnapshotService
     * @param int $snapshotsAmount
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter $pageConverter
     */
    public function __construct(
        SerializerInterface $serializer,
        ScheduleService $scheduleService,
        RequestStack $requestStack,
        ContentTypeService $contentTypeService,
        FieldDefinitionLocator $fieldDefinitionLocator,
        Scheduler $scheduler,
        ScheduleSnapshotService $scheduleSnapshotService,
        int $snapshotsAmount,
        PageConverter $pageConverter
    ) {
        $this->serializer = $serializer;
        $this->scheduleService = $scheduleService;
        $this->requestStack = $requestStack;
        $this->contentTypeService = $contentTypeService;
        $this->fieldDefinitionLocator = $fieldDefinitionLocator;
        $this->scheduler = $scheduler;
        $this->scheduleSnapshotService = $scheduleSnapshotService;
        $this->snapshotsAmount = $snapshotsAmount;
        $this->pageConverter = $pageConverter;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName('schedule') => [
                ['scheduleContent', 100],
                ['addTemplateParameters', 90],
            ],
            PageEvents::getAttributeSerializationEventName('schedule') => 'onAttributeSerialization',
            PageEvents::getAttributeDeserializationEventName('schedule') => 'onAttributeDeserialization',
            BlockResponseEvents::getBlockResponseEventName('schedule') => ['onBlockResponse', -110],
            PageEvents::PERSISTENCE_TO => ['onPersistenceTo', 0],
            KernelEvents::RESPONSE => ['onResponse', 0],
        ];
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\AttributeSerializationEvent $event
     */
    public function onAttributeSerialization(AttributeSerializationEvent $event): void
    {
        $deserializedValue = $event->getDeserializedValue();

        if (
            ScheduleBlock::ATTRIBUTE_LOADED_SNAPSHOT === $event->getAttributeIdentifier()
            && empty($deserializedValue)
        ) {
            $event->setSerializedValue('');

            return;
        }

        $serializedValue = $this->serializer->serialize($deserializedValue, 'json');

        $event->setSerializedValue($serializedValue);
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\AttributeSerializationEvent $event
     */
    public function onAttributeDeserialization(AttributeSerializationEvent $event): void
    {
        if (!isset(ScheduleBlock::SERIALIZATION_MAP[$event->getAttributeIdentifier()])) {
            $event->setDeserializedValue($event->getSerializedValue());

            return;
        }

        // @todo UI for some reason doesn't work with `null` values properly
        if (
            ScheduleBlock::ATTRIBUTE_LOADED_SNAPSHOT === $event->getAttributeIdentifier()
            && empty($event->getSerializedValue())
        ) {
            $event->setDeserializedValue(null);

            return;
        }

        $event->setDeserializedValue($this->serializer->deserialize(
            (string) $event->getSerializedValue(),
            ScheduleBlock::SERIALIZATION_MAP[$event->getAttributeIdentifier()],
            'json'
        ));
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     *
     * @throws \Exception
     */
    public function scheduleContent(PreRenderEvent $event): void
    {
        $blockValue = $event->getBlockValue();
        $context = $event->getBlockContext();

        // schedule only for Content View context, Page Builder will handle scheduling in other cases
        if (!$context instanceof ContentViewBlockContext) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        $pagePreviewParameters = $request->attributes->get('page_preview', []);
        $date = isset($pagePreviewParameters['reference_timestamp'])
            ? DateTime::createFromFormat('U', $pagePreviewParameters['reference_timestamp'])
            : new DateTime();

        $this->scheduleService->initializeScheduleData($blockValue);
        $this->scheduleSnapshotService->restoreFromSnapshot($blockValue, $date);
        $this->scheduler->scheduleToDate($blockValue, $date);
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     *
     * @throws \Exception
     */
    public function addTemplateParameters(PreRenderEvent $event): void
    {
        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $renderRequest */
        $renderRequest = $event->getRenderRequest();
        $parameters = $renderRequest->getParameters();
        $blockValue = $event->getBlockValue();

        /** @var \Ibexa\FieldTypePage\ScheduleBlock\Slot[] $slots */
        $slots = $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_SLOTS)->getValue();

        $parameters['items'] = [];
        foreach ($slots as $slot) {
            $item = $slot->getItem();
            if (null === $item || $item->getLocation() instanceof UnavailableLocation) {
                continue;
            }

            $parameters['items'][] = $item->getLocation();
        }

        $renderRequest->setParameters($parameters);
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\BlockResponseEvent $event
     *
     * @throws \Exception
     */
    public function onBlockResponse(BlockResponseEvent $event): void
    {
        $blockValue = $event->getBlockValue();
        $request = $event->getRequest();
        $response = $event->getResponse();

        $parameters = $request->attributes->get('page_preview', []);
        $date = isset($parameters['reference_timestamp'])
            ? DateTimeImmutable::createFromFormat('U', $parameters['reference_timestamp'])
            : new DateTimeImmutable();

        $nextEvent = $this->getNextEvent($blockValue, $date);

        if (null === $nextEvent) {
            return;
        }

        $ttl = $nextEvent->getDateTime()->getTimestamp() - $date->getTimestamp();

        if ($response->getTtl() <= $ttl) {
            return;
        }

        $response->setMaxAge($ttl);
        $response->setSharedMaxAge($ttl);
    }

    /**
     * @param \Ibexa\FieldTypePage\Event\PageToPersistenceEvent $event
     *
     * @throws \Exception
     */
    public function onPersistenceTo(PageToPersistenceEvent $event): void
    {
        /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value $fieldValue */
        $fieldValue = $event->getSpiFieldTypeValue();
        $page = $fieldValue->getPage();

        // we need to set time to exactly 0 seconds to avoid issues
        // when time is slightly out of sync between UI and webserver
        $snapshotStartingDate = new DateTime();
        $snapshotStartingDate->setTime(
            (int) $snapshotStartingDate->format('H'),
            (int) $snapshotStartingDate->format('i'),
            0,
            0
        );

        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                if ('schedule' !== $block->getType()) {
                    continue;
                }

                $this->scheduleSnapshotService->createSnapshots($block, $snapshotStartingDate, $this->snapshotsAmount);
            }
        }

        $event->setValue($this->pageConverter->toArray($page));
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Exception
     */
    public function onResponse(ResponseEvent $event): void
    {
        $view = $event->getRequest()->attributes->get('view');

        if (!$view instanceof ContentValueView) {
            return;
        }

        if (!$view instanceof CachableView || !$view->isCacheEnabled()) {
            return;
        }

        $content = $view->getContent();
        $contentType = $this->contentTypeService->loadContentType($content->contentInfo->contentTypeId);
        $fieldDefinition = $this->fieldDefinitionLocator->locate($content, $contentType);

        // Content doesn't have Page FieldType
        if (null === $fieldDefinition) {
            return;
        }

        $response = $event->getResponse();

        /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value $fieldValue */
        $fieldValue = $content->getFieldValue($fieldDefinition->identifier);
        $page = $fieldValue->getPage();
        $now = new DateTimeImmutable();

        $scheduleBlockNextEventDates = [];
        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $blockValue) {
                if ('schedule' !== $blockValue->getType()) {
                    continue;
                }

                $nextEvent = $this->getNextEvent($blockValue, $now);

                if (null === $nextEvent) {
                    continue;
                }

                $scheduleBlockNextEventDates[] = $nextEvent->getDateTime()->getTimestamp();
            }
        }

        if (empty($scheduleBlockNextEventDates)) {
            return;
        }

        $nextPageEvent = min($scheduleBlockNextEventDates);
        $ttl = $nextPageEvent - $now->getTimestamp();

        if ($response->isCacheable() && $response->getTtl() <= $ttl) {
            return;
        }

        $response->setPublic();
        $response->setSharedMaxAge($ttl);
        $response->setMaxAge($ttl);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \DateTimeInterface $date
     *
     * @return \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface|null
     */
    private function getNextEvent(BlockValue $blockValue, DateTimeInterface $date): ?EventInterface
    {
        $events = $this->scheduler->sortEvents(
            $blockValue->getAttribute(ScheduleBlock::ATTRIBUTE_EVENTS)->getValue() ?? []
        );

        if (empty($events)) {
            return null;
        }

        $events = $this->scheduler->filterEvents($events, $date, null);
        $nextEvent = reset($events);

        if (false === $nextEvent) {
            return null;
        }

        return $nextEvent;
    }
}

class_alias(ScheduleBlockListener::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Event\Listener\ScheduleBlockListener');
