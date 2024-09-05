<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Scheduler\Controller;

use DateTimeImmutable;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Scheduler\Form\Data\DateBasedHideCancelData;
use Ibexa\Scheduler\Form\Data\DateBasedHideData;
use Ibexa\Scheduler\Form\Type\DateBasedHideCancelType;
use Ibexa\Scheduler\Form\Type\DateBasedHideType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DateBasedContentController extends AbstractController
{
    /** @var \Ibexa\Core\Helper\TranslationHelper */
    private $translationHelper;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Ibexa\AdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \Ibexa\Contracts\Scheduler\Repository\DateBasedHideServiceInterface */
    private $dateBasedHideService;

    public function __construct(
        TranslationHelper $translationHelper,
        ContentService $contentService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler,
        DateBasedHideServiceInterface $dateBasedHideService
    ) {
        $this->translationHelper = $translationHelper;
        $this->contentService = $contentService;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
        $this->dateBasedHideService = $dateBasedHideService;
    }

    public function scheduleHideAction(Request $request): Response
    {
        $form = $this->createForm(DateBasedHideType::class);
        $form->handleRequest($request);
        $result = null;

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (DateBasedHideData $data) {
                $location = $data->getLocation();
                $versionInfo = $data->getVersionInfo();
                $contentInfo = $versionInfo->getContentInfo();
                $hideTimestamp = $data->getTimestamp();
                $isHideNow = empty($hideTimestamp);
                $contentName = $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo);

                if (true === $contentInfo->isHidden) {
                    $this->notificationHandler->error(
                        /** @Desc("Cannot hide or schedule hiding the currently hidden content: '%name%'.") */
                        'schedule.content.hide_hidden.error',
                        ['%name%' => $contentName],
                        'ibexa_scheduler'
                    );

                    return $this->redirectToLocation($location);
                }

                if ($isHideNow) {
                    if (true === $contentInfo->isPublished()) {
                        $this->dateBasedHideService->unscheduleHide($contentInfo->id);
                        $this->contentService->hideContent($contentInfo);
                    }

                    $this->notificationHandler->success(
                        /** @Desc("Content item '%name%' hidden.") */
                        'schedule.content.hide_now.success',
                        ['%name%' => $contentName],
                        'ibexa_scheduler'
                    );

                    return $this->redirectToLocation($location);
                }

                $when = DateTimeImmutable::createFromFormat('U', (string)$hideTimestamp);

                $this->dateBasedHideService->scheduleHide($location->contentId, $when);

                $this->notificationHandler->success(
                    /** @Desc("Scheduled Content item '%name%' to hide.") */
                    'schedule.content.schedule_hide.success',
                    ['%name%' => $contentName],
                    'ibexa_scheduler'
                );

                return $this->redirectToLocation($location);
            });
        }

        return $result instanceof Response
            ? $result
            : $this->redirectToRoute('ibexa.dashboard');
    }

    public function scheduleHideCancelAction(Request $request): Response
    {
        $form = $this->createForm(DateBasedHideCancelType::class);
        $form->handleRequest($request);
        $result = null;

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (DateBasedHideCancelData $data) {
                $location = $data->getLocation();
                $versionInfo = $data->getVersionInfo();
                $contentInfo = $versionInfo->getContentInfo();
                $contentName = $this->translationHelper->getTranslatedContentNameByContentInfo($contentInfo);

                $this->dateBasedHideService->unscheduleHide($contentInfo->id);

                $this->notificationHandler->success(
                    /** @Desc("Canceled scheduled hiding of Content item '%name%'.") */
                    'schedule.content.cancel_schedule_hide.success',
                    ['%name%' => $contentName],
                    'ibexa_scheduler'
                );

                return $this->redirectToLocation($location);
            });
        }

        return $result instanceof Response ? $result : $this->redirectToRoute('ibexa.dashboard');
    }

    private function redirectToLocation(Location $location, string $uriFragment = ''): RedirectResponse
    {
        if ($location === null) {
            return $this->redirectToRoute('ibexa.dashboard');
        }

        return $this->redirectToRoute('ibexa.content.view', [
            'contentId' => $location->contentId,
            'locationId' => $location->id,
            '_fragment' => $uriFragment,
        ]);
    }
}

class_alias(DateBasedContentController::class, 'EzSystems\DateBasedPublisherBundle\Controller\DateBasedContentController');
