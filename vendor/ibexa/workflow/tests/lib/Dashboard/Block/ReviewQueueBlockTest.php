<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Workflow\Dashboard\Block;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Workflow\Registry\WorkflowDefinitionMetadataRegistryInterface;
use Ibexa\Contracts\Workflow\Service\WorkflowServiceInterface;
use Ibexa\Workflow\Builder\CreatorsMapBuilder;
use Ibexa\Workflow\Builder\StagesMapBuilder;
use Ibexa\Workflow\Dashboard\Block\ReviewQueueBlock;
use Ibexa\Workflow\Value\WorkflowMetadata;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\SupportStrategy\WorkflowSupportStrategyInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @covers \Ibexa\Workflow\Dashboard\Block\ReviewQueueBlock
 */
final class ReviewQueueBlockTest extends TestCase
{
    public function testRender(): void
    {
        $templateMock = '{{ pager_options.routeName }} | {{ pager_options.pageParameter }}';
        $twigStub = new Environment(
            new ArrayLoader(
                [
                    '@ibexadesign/ibexa_workflow/admin/dashboard/block/review_queue/block.html.twig' => $templateMock,
                ]
            )
        );

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

        $workflowDefinitionMetadataRegistry = $this->createMock(WorkflowDefinitionMetadataRegistryInterface::class);
        $workflowDefinitionMetadataRegistry->method('getAllWorkflowMetadata')->willReturn([new WorkflowMetadata()]);

        $block = new ReviewQueueBlock(
            $twigStub,
            $this->createMock(StagesMapBuilder::class),
            $this->createMock(CreatorsMapBuilder::class),
            $this->createMock(WorkflowServiceInterface::class),
            $this->createMock(WorkflowSupportStrategyInterface::class),
            $workflowDefinitionMetadataRegistry,
            $requestStackMock,
            $this->createMock(PermissionResolver::class),
            $configResolverMock,
            $this->createMock(UserService::class)
        );

        self::assertSame(
            // see $templateMock
            'app.page | [review_queue_page]',
            $block->render(['pager_options' => ['routeName' => 'app.page']])
        );
    }
}
