<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Controller;

use DateTime;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\ScheduleBlock\Scheduler;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleService;
use Ibexa\FieldTypePage\ScheduleBlock\ScheduleSnapshotService;
use Ibexa\PageBuilder\Block\ScheduleBlock\ConfigurationDataGenerator;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ScheduleBlockController extends Controller
{
    /** @var \JMS\Serializer\SerializerInterface */
    private $serializer;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\Scheduler */
    private $scheduler;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\ScheduleSnapshotService */
    private $scheduleSnapshotService;

    /** @var \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService */
    private $scheduleService;

    /**
     * @param \JMS\Serializer\SerializerInterface $serializer
     * @param \Ibexa\FieldTypePage\ScheduleBlock\Scheduler $scheduler
     * @param \Ibexa\FieldTypePage\ScheduleBlock\ScheduleSnapshotService $scheduleSnapshotService
     * @param \Ibexa\FieldTypePage\ScheduleBlock\ScheduleService $scheduleService
     */
    public function __construct(
        SerializerInterface $serializer,
        Scheduler $scheduler,
        ScheduleSnapshotService $scheduleSnapshotService,
        ScheduleService $scheduleService
    ) {
        $this->serializer = $serializer;
        $this->scheduler = $scheduler;
        $this->scheduleSnapshotService = $scheduleSnapshotService;
        $this->scheduleService = $scheduleService;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Exception
     */
    public function listContentAction(Request $request): JsonResponse
    {
        $dataGenerator = new ConfigurationDataGenerator();
        $now = new DateTime();

        $blockValueJson = $request->get('blockValue');
        /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue */
        $blockValue = $this->serializer->deserialize($blockValueJson, BlockValue::class, 'json');

        $this->scheduleService->initializeScheduleData($blockValue);
        $this->scheduleSnapshotService->restoreFromSnapshot($blockValue, $now);
        $this->scheduler->scheduleToDate($blockValue, $now);

        $data = $dataGenerator->generate($blockValue);
        $serializedData = $this->serializer->serialize($data, 'json');

        return new JsonResponse($serializedData, 200, [], true);
    }
}

class_alias(ScheduleBlockController::class, 'EzSystems\EzPlatformPageBuilderBundle\Controller\ScheduleBlockController');
