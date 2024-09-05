<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Exception\MissingReportAttributesException;
use Ibexa\Personalization\Exception\UnsupportedFormatException;
use Ibexa\Personalization\Factory\DateTime\GranularityFactory;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Report\ReportService;
use Ibexa\Personalization\Service\Report\ReportServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ReportController extends AbstractPersonalizationController
{
    private const PARAMETER_DATE_INTERVAL = 'date_interval';
    private const PARAMETER_END_DATE = 'end_date';
    private const CONTENT_TYPES = [
        ReportService::FORMAT_XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    private ReportServiceInterface $reportService;

    private GranularityFactory $granularityFactory;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        CustomerTypeCheckerInterface $customerTypeChecker,
        EventDispatcherInterface $eventDispatcher,
        SettingServiceInterface $settingService,
        ReportServiceInterface $reportService,
        GranularityFactory $granularityFactory,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        parent::__construct($permissionChecker, $customerTypeChecker, $eventDispatcher, $settingService);

        $this->reportService = $reportService;
        $this->granularityFactory = $granularityFactory;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function recommendationDetailedReportAction(Request $request, int $customerId): Response
    {
        $this->performAccessCheck($customerId);

        if (!$request->get(self::PARAMETER_DATE_INTERVAL)) {
            throw new RuntimeException(
                sprintf(
                    'Missing mandatory parameter: %s',
                    self::PARAMETER_DATE_INTERVAL
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        $granularityDateTimeRange = $this->getGranularityDateTimeRange($request);
        $format = ReportService::FORMAT_XLSX;

        try {
            $report = $this->reportService->getRecommendationDetailedReport(
                $customerId,
                $granularityDateTimeRange,
                $format
            );

            return $this->downloadReport($report->getContent(), $report->getName(), $format);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->redirectToRoute('ibexa.personalization.dashboard');
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function revenueReportAction(Request $request, int $customerId): Response
    {
        $this->performAccessCheck($customerId);

        if (!$request->get(self::PARAMETER_DATE_INTERVAL)) {
            throw new RuntimeException(
                sprintf(
                    'Missing mandatory parameter: %s',
                    self::PARAMETER_DATE_INTERVAL
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        $granularityDateTimeRange = $this->getGranularityDateTimeRange($request);
        $user = $this->getUser();
        $email = null;

        if ($user instanceof UserInterface) {
            $email = $user->getAPIUser()->email;
        }

        $dateTimeRange = new DateTimeRange(
            $granularityDateTimeRange->getFromDate(),
            $granularityDateTimeRange->getToDate(),
        );

        $format = ReportService::FORMAT_XLSX;

        try {
            $report = $this->reportService->getRevenueReport(
                $customerId,
                $dateTimeRange,
                $format,
                $email
            );

            if ($report->isDeferred()) {
                return new Response(null, Response::HTTP_ACCEPTED);
            }

            if (
                null === $report->getName()
                || null === $report->getContent()
            ) {
                $attributes = sprintf('name: %s, content: %s', $report->getName(), $report->getContent());

                throw new MissingReportAttributesException(
                    sprintf('Report has missing attributes - %s', $attributes)
                );
            }

            return $this->downloadReport($report->getContent(), $report->getName(), $format);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                return $this->redirectToRoute('ibexa.personalization.dashboard');
            }

            throw $exception;
        }
    }

    public function downloadReport(string $content, string $fileName, string $format): Response
    {
        if (!array_key_exists($format, self::CONTENT_TYPES)) {
            throw new UnsupportedFormatException(
                $format,
                array_keys(self::CONTENT_TYPES)
            );
        }

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileName . '.' . $format
        );

        return new Response($content, Response::HTTP_OK, [
            'Content-Disposition' => $disposition,
            'Content-Type' => self::CONTENT_TYPES[$format],
        ]);
    }

    private function getGranularityDateTimeRange(Request $request): GranularityDateTimeRange
    {
        if ($request->get(self::PARAMETER_END_DATE)) {
            $endDateTimestamp = (int)$request->get(self::PARAMETER_END_DATE);

            if ($endDateTimestamp > 0) {
                return $this->granularityFactory->createFromEndDateTimestampAndInterval(
                    $request->get(self::PARAMETER_DATE_INTERVAL),
                    (int)$request->get(self::PARAMETER_END_DATE),
                );
            }
        }

        return $this->granularityFactory->createFromInterval(
            $request->get(self::PARAMETER_DATE_INTERVAL)
        );
    }
}

class_alias(ReportController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\ReportController');
