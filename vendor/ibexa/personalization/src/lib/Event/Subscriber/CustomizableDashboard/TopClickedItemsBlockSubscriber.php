<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Subscriber\CustomizableDashboard;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcher;
use Ibexa\Personalization\Form\Data\DashboardData;
use Ibexa\Personalization\Form\Data\PopularityDurationChoiceData;
use Ibexa\Personalization\Form\Type\Block\TopClickedItemsType;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Performance\RecommendationPerformanceServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
final class TopClickedItemsBlockSubscriber implements EventSubscriberInterface
{
    private const BLOCK_IDENTIFIER = 'top_clicked_items';

    private RecommendationPerformanceServiceInterface $recommendationPerformanceService;

    private FormFactoryInterface $formFactory;

    private RequestStack $requestStack;

    private SecurityServiceInterface $securityService;

    public function __construct(
        RecommendationPerformanceServiceInterface $recommendationPerformanceService,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
        SecurityServiceInterface $securityService
    ) {
        $this->formFactory = $formFactory;
        $this->recommendationPerformanceService = $recommendationPerformanceService;
        $this->requestStack = $requestStack;
        $this->securityService = $securityService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::BLOCK_IDENTIFIER) => ['onBlockPreRender', -100],
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function onBlockPreRender(PreRenderEvent $event): void
    {
        $blockValue = $event->getBlockValue();
        $customerId = $this->getCustomerId($blockValue);

        try {
            $this->securityService->checkAcceptanceStatus();
        } catch (UnauthorizedException $exception) {
            $this->handleNoPermissionException($event);

            return;
        }

        $form = $this->buildDurationSelectorForm($blockValue->getId());
        // current request is a block-rendering sub-request - we expect form submission data on the main request
        $form->handleRequest($this->requestStack->getMainRequest());

        $dashboardFormData = $form->getData();
        $popularityDuration = $dashboardFormData->getPopularity()->getDuration();

        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $twigRenderRequest */
        $twigRenderRequest = $event->getRenderRequest();
        $twigRenderRequest->addParameter('form', $form->createView());
        $twigRenderRequest->addParameter(
            'popularity_list',
            $this->recommendationPerformanceService->getPopularityList(
                $customerId,
                $popularityDuration
            ),
        );
    }

    private function buildDurationSelectorForm(string $blockId): FormInterface
    {
        $dashboardTimePeriodData = new DashboardData();
        $dashboardTimePeriodData->setPopularity(
            (new PopularityDurationChoiceData())->setDuration(PopularityDataFetcher::DURATION_24H)
        );

        return $this->formFactory->createNamed(
            "top-clicked-items-$blockId",
            TopClickedItemsType::class,
            $dashboardTimePeriodData,
            [
                'method' => Request::METHOD_GET,
            ]
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getCustomerId(BlockValue $blockValue): int
    {
        $attribute = $blockValue->getAttribute('customer_id');
        if (null === $attribute || $attribute->getValue() === null) {
            throw new InvalidArgumentException('$blockValue', 'Personalization Customer ID has not been configured');
        }

        return (int)$attribute->getValue();
    }

    private function handleNoPermissionException(PreRenderEvent $event): void
    {
        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $twigRenderRequest */
        $twigRenderRequest = $event->getRenderRequest();
        $twigRenderRequest->addParameter('block_name', $event->getBlockValue()->getName());
        $twigRenderRequest->setTemplate(
            '@ibexadesign/personalization/dashboard/blocks/errors/no_permission.html.twig'
        );
    }
}
