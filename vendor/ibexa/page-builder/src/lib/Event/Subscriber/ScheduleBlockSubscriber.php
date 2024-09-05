<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use DateTime;
use DateTimeImmutable;
use Ibexa\Contracts\AdminUi\Resolver\IconPathResolverInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\ScheduleBlock\Item\UnavailableLocation;
use Ibexa\FieldTypePage\ScheduleBlock\Scheduler;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleService;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleSnapshotService;
use Ibexa\PageBuilder\Block\Context\ContentCreateBlockContext;
use Ibexa\PageBuilder\Block\Context\ContentEditBlockContext;
use Ibexa\PageBuilder\Block\Context\ContentTranslateBlockContext;
use Ibexa\PageBuilder\Block\Mapper\BlockConfigurationMapper;
use Ibexa\PageBuilder\Block\ScheduleBlock\ConfigurationDataGenerator;
use Ibexa\PageBuilder\Event\BlockConfigurationViewEvent;
use Ibexa\PageBuilder\Event\BlockConfigurationViewEvents;
use Ibexa\PageBuilder\Event\BlockPreviewEvents;
use Ibexa\PageBuilder\Event\BlockPreviewResponseEvent;
use Ibexa\PageBuilder\PageBuilder\Timeline\BasicEvent;
use Ibexa\PageBuilder\PageBuilder\Timeline\Context\PageContextInterface;
use Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent;
use Ibexa\PageBuilder\PageBuilder\Timeline\Event\TimelineEvents;
use JMS\Serializer\SerializerInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ScheduleBlockSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\PageBuilder\Block\Mapper\BlockConfigurationMapper */
    private $blockConfigurationMapper;

    /** @var \JMS\Serializer\SerializerInterface */
    private $serializer;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Scheduler */
    private $scheduler;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService */
    private $scheduleService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    /** @var \Twig\Environment */
    private $templating;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    private $translationHelper;

    /** @var \Ibexa\Contracts\AdminUi\Resolver\IconPathResolverInterface */
    private $iconPathResolver;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\ScheduleSnapshotService */
    private $scheduleSnapshotService;

    public function __construct(
        BlockConfigurationMapper $blockConfigurationMapper,
        SerializerInterface $serializer,
        Scheduler $scheduler,
        ScheduleService $scheduleService,
        ContentTypeService $contentTypeService,
        BlockDefinitionFactoryInterface $blockDefinitionFactory,
        Environment $templating,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        TranslationHelper $translationHelper,
        IconPathResolverInterface $iconPathResolver,
        ScheduleSnapshotService $scheduleSnapshotService
    ) {
        $this->blockConfigurationMapper = $blockConfigurationMapper;
        $this->serializer = $serializer;
        $this->scheduler = $scheduler;
        $this->scheduleService = $scheduleService;
        $this->contentTypeService = $contentTypeService;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->translationHelper = $translationHelper;
        $this->iconPathResolver = $iconPathResolver;
        $this->scheduleSnapshotService = $scheduleSnapshotService;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BlockConfigurationViewEvents::getBlockConfigurationViewEventName('schedule') => 'onBlockConfiguration',
            BlockPreviewEvents::getBlockPreviewResponseEventName('schedule') => ['onBlockPreviewResponse', 0],
            TimelineEvents::COLLECT_EVENTS => 'onTimelineEventsCollect',
            BlockRenderEvents::getBlockPreRenderEventName('schedule') => ['scheduleContent', 100],
        ];
    }

    /**
     * @param \Ibexa\PageBuilder\Event\BlockConfigurationViewEvent $event
     *
     * @throws \Exception
     */
    public function onBlockConfiguration(BlockConfigurationViewEvent $event): void
    {
        $configurationDataGenerator = new ConfigurationDataGenerator();
        $now = new DateTime();

        $view = $event->getBlockConfigurationView();
        $blockConfiguration = $event->getBlockConfiguration();
        $blockValue = $this->blockConfigurationMapper->mapToBlockValue($blockConfiguration);

        $this->scheduleService->initializeScheduleData($blockValue);
        $this->scheduleSnapshotService->restoreFromSnapshot($blockValue, $now);
        $this->scheduler->scheduleToDate($blockValue, $now);

        $data = $configurationDataGenerator->generate($blockValue);

        $view->addParameters([
            'serialized_data' => $this->serializer->serialize($data, 'json'),
        ]);
    }

    /**
     * @param \Ibexa\PageBuilder\Event\BlockPreviewResponseEvent $event
     *
     * @throws \Exception
     */
    public function onBlockPreviewResponse(BlockPreviewResponseEvent $event): void
    {
        $pagePreviewParameters = $event->getPagePreviewParameters();
        $date = isset($pagePreviewParameters['referenceTimestamp'])
            ? DateTimeImmutable::createFromFormat('U', $pagePreviewParameters['referenceTimestamp'])
            : new DateTimeImmutable();

        $blockValue = $event->getBlockValue();

        $this->scheduleService->initializeScheduleData($blockValue);
        $this->scheduleSnapshotService->restoreFromSnapshot($blockValue, $date);
        $this->scheduler->scheduleToDate($blockValue, $date);
    }

    /**
     * @param \Ibexa\PageBuilder\PageBuilder\Timeline\Event\ContentTimelineEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onTimelineEventsCollect(ContentTimelineEvent $event): void
    {
        $dataGenerator = new ConfigurationDataGenerator();
        $timelineEvents = $event->getTimelineEvents();
        $context = $event->getContext();

        if (!$context instanceof PageContextInterface) {
            return;
        }

        $page = $context->getPage();

        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $block) {
                if ($block->getType() !== 'schedule') {
                    continue;
                }

                // @todo refactor to not use DataGenerator
                $data = $dataGenerator->generate($block);
                /** @var \Ibexa\FieldTypePage\ScheduleBlock\Item\Item $item */
                foreach ($data['lists']['queue'] as $item) {
                    $location = $item->getLocation();

                    if ($location instanceof UnavailableLocation) {
                        continue;
                    }

                    $itemContentType = $this->contentTypeService->loadContentType($location->contentInfo->contentTypeId);
                    $eventName = $this->translator->trans(
                        /** @Desc("%contentTypeName% '%contentName%' added to block %blockName%") */
                        'event.schedule_block.item_added.title',
                        [
                            '%contentTypeName%' => $itemContentType->getName(),
                            '%blockName%' => $block->getName(),
                            '%contentName%' => $this->translationHelper->getTranslatedContentName($location->getContent()),
                        ],
                        'ibexa_page_builder_timeline_events'
                    );
                    $blockDefinition = $this->blockDefinitionFactory->getBlockDefinition($block->getType());
                    $timelineEvents[] = new BasicEvent(
                        $eventName,
                        $this->templating->render(
                            '@IbexaPageBuilder/page_builder/timeline/events/schedule_block/item_added_event_description.twig',
                            [
                                'name' => $eventName,
                                'date' => $item->getAdditionDate(),
                                'block_value' => $block,
                                'block_definition' => $blockDefinition,
                            ]
                        ),
                        $item->getAdditionDate(),
                        $this->iconPathResolver->resolve('block-visible')
                    );
                }
            }
        }

        $event->setTimelineEvents($timelineEvents);
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent $event
     *
     * @throws \Exception
     */
    public function scheduleContent(PreRenderEvent $event): void
    {
        $context = $event->getBlockContext();

        // schedule when in PageBuilder only
        if (
            !$context instanceof ContentEditBlockContext
            && !$context instanceof ContentCreateBlockContext
            && !$context instanceof ContentTranslateBlockContext
        ) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $previewRequestParameters = $request->get('parameters', []);

        if (empty($previewRequestParameters['pagePreview']['referenceTimestamp'])) {
            return;
        }

        $date = DateTimeImmutable::createFromFormat('U', $previewRequestParameters['pagePreview']['referenceTimestamp']);
        $blockValue = $event->getBlockValue();

        $this->scheduleService->initializeScheduleData($blockValue);
        $this->scheduleSnapshotService->restoreFromSnapshot($blockValue, $date);
        $this->scheduler->scheduleToDate($blockValue, $date);
    }
}

class_alias(ScheduleBlockSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\ScheduleBlockSubscriber');
