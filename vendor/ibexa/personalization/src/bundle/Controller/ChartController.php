<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Factory\Form\PersonalizationFormFactoryInterface;
use Ibexa\Personalization\Form\Data\DashboardData;
use Ibexa\Personalization\Form\Data\DateTimeRangeData;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Chart\ChartServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Chart\ChartParameters;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ChartController extends AbstractPersonalizationAjaxController
{
    private ChartServiceInterface $chartService;

    private PersonalizationFormFactoryInterface $formFactory;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        SettingServiceInterface $settingService,
        ChartServiceInterface $chartService,
        PersonalizationFormFactoryInterface $formFactory
    ) {
        parent::__construct($permissionChecker, $settingService);

        $this->chartService = $chartService;
        $this->formFactory = $formFactory;
    }

    public function getDataAction(Request $request, int $customerId): JsonResponse
    {
        $errors = $this->performAccessCheck($request, $customerId);

        if (count($errors) > 0) {
            return new JsonResponse($errors, Response::HTTP_FORBIDDEN);
        }

        $dashboardData = new DashboardData();
        $dashboardData->setChart(new DateTimeRangeData());

        $form = $this->formFactory->createDashboardTimePeriodForm($customerId, $dashboardData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange $period */
            $period = $form->getData()->getChart()->getPeriod();

            try {
                return new JsonResponse(
                    [
                        'charts' => $this->chartService->getCharts(
                            $customerId,
                            new ChartParameters($period)
                        ),
                    ]
                );
            } catch (BadResponseException $exception) {
                if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                    return new JsonResponse([], Response::HTTP_SERVICE_UNAVAILABLE);
                }
            }
        }

        return new JsonResponse($this->getFormErrors($form), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

class_alias(ChartController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\ChartController');
