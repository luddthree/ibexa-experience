<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Controller;

use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcher;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Factory\DateTime\GranularityFactoryInterface;
use Ibexa\Personalization\Factory\Form\PersonalizationFormFactoryInterface;
use Ibexa\Personalization\Form\Data\DashboardData;
use Ibexa\Personalization\Form\Data\DateTimeRangeData;
use Ibexa\Personalization\Form\Data\MultiCustomerAccountsData;
use Ibexa\Personalization\Form\Data\PopularityDurationChoiceData;
use Ibexa\Personalization\Pagination\Pagerfanta\RevenueDetailsListAdapter;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Service\Chart\ChartServiceInterface;
use Ibexa\Personalization\Service\Performance\RecommendationPerformanceServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Chart\ChartParameters;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Performance\RecommendationSummary;
use Ibexa\Personalization\Value\Performance\Revenue\RevenueDetailsList;
use Ibexa\Personalization\Value\TimePeriod;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class DashboardController extends AbstractPersonalizationController
{
    private const COMMERCE_TEMPLATE = '@ibexadesign/personalization/dashboard/commerce.html.twig';
    private const PUBLISHER_TEMPLATE = '@ibexadesign/personalization/dashboard/publisher.html.twig';
    private const DEFAULT_PAGE = '1';

    private RecommendationPerformanceServiceInterface $recommendationPerformanceService;

    private ChartServiceInterface $chartService;

    private GranularityFactoryInterface $granularityFactory;

    private PersonalizationFormFactoryInterface $formFactory;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        PermissionCheckerInterface $permissionChecker,
        CustomerTypeCheckerInterface $customerTypeChecker,
        EventDispatcherInterface $eventDispatcher,
        SettingServiceInterface $settingService,
        RecommendationPerformanceServiceInterface $recommendationPerformanceService,
        GranularityFactoryInterface $granularityFactory,
        ChartServiceInterface $chartService,
        PersonalizationFormFactoryInterface $formFactory,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        parent::__construct($permissionChecker, $customerTypeChecker, $eventDispatcher, $settingService);

        $this->recommendationPerformanceService = $recommendationPerformanceService;
        $this->chartService = $chartService;
        $this->granularityFactory = $granularityFactory;
        $this->formFactory = $formFactory;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Personalization\Exception\InvalidInstallationKeyException
     * @throws \JsonException
     * @throws \Ibexa\Core\Base\Exceptions\UnauthorizedException
     */
    public function dashboardAction(Request $request, int $customerId): Response
    {
        if (($result = $this->performCredentialsCheck()) instanceof Response) {
            return $result;
        }

        $this->performAccessCheck($customerId);

        $defaultDateTimeRange = $this->granularityFactory->createFromInterval(TimePeriod::LAST_24_HOURS);
        $dashboardTimePeriodData = new DashboardData();
        $dashboardTimePeriodData
            ->setChart((new DateTimeRangeData())->setPeriod($defaultDateTimeRange))
            ->setPopularity((new PopularityDurationChoiceData())->setDuration(PopularityDataFetcher::DURATION_24H));

        try {
            if ($this->customerTypeChecker->isCommerce($customerId)) {
                $page = (int) $request->query->get('page', self::DEFAULT_PAGE);
                $revenueDateTimeRange = $defaultDateTimeRange;
                $dashboardTimePeriodData->setRevenue(
                    (new DateTimeRangeData())->setPeriod($revenueDateTimeRange)
                );
                $form = $this->formFactory->createDashboardTimePeriodForm($customerId, $dashboardTimePeriodData);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $revenueDateTimeRange = $form->getData()->getRevenue()->getPeriod();
                }

                $revenueDetailsList = $this->getRevenueDetailsList($customerId, $revenueDateTimeRange);
                $pagerfanta = $this->getPagerfanta($revenueDetailsList, $page);

                return $this->renderTemplate(
                    $customerId,
                    self::COMMERCE_TEMPLATE,
                    array_merge(
                        $this->getCommonElements($customerId, $form),
                        [
                            'pager' => $pagerfanta,
                            'form' => $form->createView(),
                        ],
                    )
                );
            }

            $form = $this->formFactory->createDashboardTimePeriodForm($customerId, $dashboardTimePeriodData);
            $form->handleRequest($request);

            return $this->renderTemplate(
                $customerId,
                self::PUBLISHER_TEMPLATE,
                $this->getCommonElements($customerId, $form),
            );
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                $this->notificationHandler->warning(
                    /** @Desc("Recommendation engine is not available. Please try again later.") */
                    'recommendation_engine_is_not_available',
                    [],
                    'ibexa_personalization'
                );

                $template = $this->customerTypeChecker->isCommerce($customerId)
                    ? self::COMMERCE_TEMPLATE
                    : self::PUBLISHER_TEMPLATE;

                $form = $this->formFactory->createDashboardTimePeriodForm($customerId, $dashboardTimePeriodData);

                return $this->renderTemplate(
                    $customerId,
                    $template,
                    [
                        'summary' => RecommendationSummary::fromArray([]),
                        'form' => $form->createView(),
                        'charts' => null,
                        'pager' => null,
                        'popularity_list' => null,
                        'customer_switcher' => $this->formFactory->createMultiCustomerAccountsForm(
                            new MultiCustomerAccountsData($customerId)
                        )->createView(),
                    ]
                );
            }

            throw $exception;
        }
    }

    private function getRevenueDetailsList(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange
    ): RevenueDetailsList {
        return $this->recommendationPerformanceService->getRevenueDetailsList(
            $customerId,
            new DateTimeRange(
                $granularityDateTimeRange->getFromDate(),
                $granularityDateTimeRange->getToDate(),
            )
        );
    }

    /**
     * @return \Pagerfanta\Pagerfanta<\Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails>
     */
    private function getPagerfanta(
        RevenueDetailsList $revenueDetailsList,
        int $page
    ): Pagerfanta {
        $pagerfanta = new Pagerfanta(
            new RevenueDetailsListAdapter($revenueDetailsList)
        );
        $pagerfanta->setCurrentPage($page);

        $maxPerPage = $this->getConfigResolver()->getParameter('personalization.pagination.limit');
        $pagerfanta->setMaxPerPage($maxPerPage);

        return $pagerfanta;
    }

    /**
     * @phpstan-return array{
     *  'form': \Symfony\Component\Form\FormView,
     *  'summary': \Ibexa\Personalization\Value\Performance\RecommendationSummary,
     *  'charts': string,
     *  'customer_switcher': \Symfony\Component\Form\FormView
     * }
     *
     * @throws \JsonException
     */
    private function getCommonElements(
        int $customerId,
        FormInterface $form
    ): array {
        /** @var \Ibexa\Personalization\Form\Data\DashboardData $dashboardFormData */
        $dashboardFormData = $form->getData();

        /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange $dateTimeRange */
        $dateTimeRange = $dashboardFormData->getChart()->getPeriod();

        /** @var string $popularityDuration */
        $popularityDuration = $dashboardFormData->getPopularity()->getDuration();

        return [
            'form' => $form->createView(),
            'summary' => $this->recommendationPerformanceService->getRecommendationSummary(
                $customerId
            ),
            'popularity_list' => $this->recommendationPerformanceService->getPopularityList(
                $customerId,
                $popularityDuration
            ),
            'charts' => json_encode(
                $this->chartService->getCharts(
                    $customerId,
                    new ChartParameters($dateTimeRange),
                ),
                JSON_THROW_ON_ERROR
            ),
            'customer_switcher' => $this->formFactory->createMultiCustomerAccountsForm(
                new MultiCustomerAccountsData($customerId)
            )->createView(),
        ];
    }
}

class_alias(DashboardController::class, 'Ibexa\Platform\Bundle\Personalization\Controller\DashboardController');
