<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Workflow\Tab;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Builder\CreatorsMapBuilder;
use Ibexa\Workflow\Builder\StagesMapBuilder;
use Ibexa\Workflow\Tab\MyDraftsUnderReviewTab;
use Pagerfanta\PagerfantaInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @covers \Ibexa\Workflow\Tab\MyDraftsUnderReviewTab
 */
final class MyDraftsUnderReviewTabTest extends TestCase
{
    private MyDraftsUnderReviewTab $tab;

    protected function setUp(): void
    {
        $requestStackMock = $this->createMock(RequestStack::class);
        $requestStackMock
            ->method('getCurrentRequest')
            ->willReturn(new Request())
        ;

        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock
            ->method('getParameter')
            ->willReturnMap(
                [
                    ['pagination.content_draft_limit', null, null, 10],
                ]
            )
        ;

        $this->tab = new MyDraftsUnderReviewTab(
            $this->createMock(Environment::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $requestStackMock,
            $this->createMock(WorkflowServiceInterface::class),
            $this->createMock(PermissionResolver::class),
            $this->createMock(StagesMapBuilder::class),
            $this->createMock(CreatorsMapBuilder::class),
            $this->createMock(WorkflowDefinitionMetadataRegistryInterface::class),
            $this->createMock(WorkflowSupportStrategyInterface::class),
            $configResolverMock,
            $this->createMock(UserService::class)
        );
    }

    /**
     * @return iterable<string, array{array<string, mixed>, array<string, mixed>}>
     */
    public function getDataForTestGetTemplateParameters(): iterable
    {
        $pagerOptions = [
            'pageParameter' => '[page][my_drafts_under_review_page]',
            'routeParams' => [
                '_fragment' => 'ibexa-tab-dashboard-my-my-drafts-under-review',
            ],
        ];

        yield 'no extra context' => [
            [
                'pager_options' => $pagerOptions,
            ],
            [
            ],
        ];

        yield 'extra context' => [
            [
                'pager_options' => $pagerOptions,
                'extra' => 'foo',
            ],
            [
                'extra' => 'foo',
            ],
        ];

        yield 'extra pager options' => [
            [
                'pager_options' => [
                    'routeName' => 'app.page',
                    'pageParameter' => '[page][my_drafts_under_review_page]',
                    'routeParams' => [
                        '_fragment' => 'ibexa-tab-dashboard-my-my-drafts-under-review',
                    ],
                ],
            ],
            [
                'pager_options' => ['routeName' => 'app.page'],
            ],
        ];
    }

    /**
     * @dataProvider getDataForTestGetTemplateParameters
     *
     * @param array<string, mixed> $expectedParameters
     * @param array<string, mixed> $contextParameters
     */
    public function testGetTemplateParameters(array $expectedParameters, array $contextParameters): void
    {
        $actualTemplateParameters = $this->tab->getTemplateParameters($contextParameters);

        self::assertInstanceOf(PagerfantaInterface::class, $actualTemplateParameters['pager']);

        foreach ($expectedParameters as $expectedParameterName => $expectedParameterValue) {
            self::assertSame($expectedParameterValue, $actualTemplateParameters[$expectedParameterName]);
        }
    }
}
