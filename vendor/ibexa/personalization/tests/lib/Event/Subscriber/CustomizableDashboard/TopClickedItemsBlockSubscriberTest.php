<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Event\Subscriber\CustomizableDashboard;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcher;
use Ibexa\Personalization\Event\Subscriber\CustomizableDashboard\TopClickedItemsBlockSubscriber;
use Ibexa\Personalization\Form\Data\DashboardData;
use Ibexa\Personalization\Form\Data\PopularityDurationChoiceData;
use Ibexa\Personalization\Form\Type\Block\TopClickedItemsType;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Performance\RecommendationPerformanceServiceInterface;
use Ibexa\Personalization\Value\Performance\Popularity\PopularityList;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers \Ibexa\Personalization\Event\Subscriber\CustomizableDashboard\TopClickedItemsBlockSubscriber
 */
final class TopClickedItemsBlockSubscriberTest extends TestCase
{
    public const CUSTOMER_ID = 111;

    private TopClickedItemsBlockSubscriber $subscriber;

    /** @var \Symfony\Component\Form\FormFactoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private FormFactoryInterface $formFactoryMock;

    /** @var \Ibexa\Personalization\Service\Performance\RecommendationPerformanceServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private RecommendationPerformanceServiceInterface $recommendationPerformanceServiceMock;

    /** @var \Ibexa\Personalization\Security\Service\SecurityServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SecurityServiceInterface $securityService;

    protected function setUp(): void
    {
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);
        $this->recommendationPerformanceServiceMock = $this->createMock(
            RecommendationPerformanceServiceInterface::class
        );

        $this->securityService = $this->createMock(SecurityServiceInterface::class);
        $this->subscriber = new TopClickedItemsBlockSubscriber(
            $this->recommendationPerformanceServiceMock,
            $this->formFactoryMock,
            $this->createMock(RequestStack::class),
            $this->securityService
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testOnBlockPreRender(): void
    {
        $preRenderEventMock = $this->createPreRenderEventMock();

        $twigRenderRequestMock = $this->createMock(TwigRenderRequest::class);
        $twigRenderRequestMock
            ->expects(self::at(0))
            ->method('addParameter')
            ->with('form', self::isInstanceOf(FormView::class))
        ;
        $twigRenderRequestMock
            ->expects(self::at(1))
            ->method('addParameter')
            ->with('popularity_list', self::isInstanceOf(PopularityList::class))
        ;

        $preRenderEventMock->method('getRenderRequest')->willReturn($twigRenderRequestMock);

        $this->subscriber->onBlockPreRender($preRenderEventMock);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testOnBlockPreRenderNoPermission(): void
    {
        $preRenderEventMock = $this->createPreRenderEventMock();

        $this
            ->securityService
            ->method('checkAcceptanceStatus')
            ->willThrowException($this->createMock(UnauthorizedException::class))
        ;

        $twigRenderRequestMock = $this->createMock(TwigRenderRequest::class);
        $preRenderEventMock->method('getRenderRequest')->willReturn($twigRenderRequestMock);
        $twigRenderRequestMock->method('addParameter')->with('block_name', 'Block Name');
        $twigRenderRequestMock->method('setTemplate')->with(
            '@ibexadesign/personalization/dashboard/blocks/errors/no_permission.html.twig'
        );

        $this->recommendationPerformanceServiceMock->expects(self::never())->method('getPopularityList');

        $this->subscriber->onBlockPreRender($preRenderEventMock);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                BlockRenderEvents::getBlockPreRenderEventName('top_clicked_items') => ['onBlockPreRender', -100],
            ],
            $this->subscriber::getSubscribedEvents()
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject&\Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent
     */
    private function createPreRenderEventMock(): PreRenderEvent
    {
        $preRenderEventMock = $this->createMock(PreRenderEvent::class);
        $blockValueMock = $this->createMock(BlockValue::class);

        $blockValueMock->method('getId')->willReturn('123');
        $blockValueMock->method('getName')->willReturn('Block Name');
        $customerIdAttributeMock = $this->createMock(Attribute::class);
        $customerIdAttributeMock->method('getValue')->willReturn(self::CUSTOMER_ID);
        $blockValueMock->method('getAttribute')->with('customer_id')->willReturn($customerIdAttributeMock);
        $preRenderEventMock->method('getBlockValue')->willReturn($blockValueMock);

        $dashboardData = new DashboardData();
        $dashboardData
            ->setPopularity((new PopularityDurationChoiceData())->setDuration(PopularityDataFetcher::DURATION_24H))
        ;

        $formMock = $this->createMock(FormInterface::class);
        $formMock->method('getData')->willReturn($dashboardData);
        $formMock->method('createView')->willReturn($this->createMock(FormView::class));

        $this
            ->formFactoryMock
            ->method('createNamed')
            ->with(
                'top-clicked-items-123',
                TopClickedItemsType::class,
                $dashboardData,
                ['method' => 'GET']
            )
            ->willReturn($formMock)
        ;

        $this
            ->recommendationPerformanceServiceMock
            ->method('getPopularityList')
            ->with(self::CUSTOMER_ID)
            ->willReturn(new PopularityList([]))
        ;

        return $preRenderEventMock;
    }
}
