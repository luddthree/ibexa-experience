<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Service\Decorator;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\Decorator\TaxonomyEntryAssignmentServiceDecorator;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignmentCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TaxonomyEntryAssignmentServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_ASSIGNMENT_ID = 1;

    protected function createDecorator(TaxonomyEntryAssignmentServiceInterface $service): TaxonomyEntryAssignmentServiceInterface
    {
        return new class($service) extends TaxonomyEntryAssignmentServiceDecorator {
        };
    }

    /**
     * @return \Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createServiceMock(): MockObject
    {
        return $this->createMock(TaxonomyEntryAssignmentServiceInterface::class);
    }

    public function testLoadAssignmentByIdDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [self::EXAMPLE_ASSIGNMENT_ID];

        $serviceMock
            ->expects($this->once())
            ->method('loadAssignmentById')
            ->with(...$parameters)
            ->willReturn($this->createMock(TaxonomyEntryAssignment::class));

        $decoratedService->loadAssignmentById(...$parameters);
    }

    public function testLoadAssignmentsDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(Content::class), 1];

        $serviceMock
            ->expects($this->once())
            ->method('loadAssignments')
            ->with(...$parameters)
            ->willReturn($this->createMock(TaxonomyEntryAssignmentCollection::class));

        $decoratedService->loadAssignments(...$parameters);
    }

    public function testAssignToContentDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(Content::class),
            $this->createMock(TaxonomyEntry::class),
            1,
        ];

        $serviceMock
            ->expects($this->once())
            ->method('assignToContent')
            ->with(...$parameters);

        $decoratedService->assignToContent(...$parameters);
    }

    public function unassignFromContent(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(Content::class),
            $this->createMock(TaxonomyEntry::class),
            1,
        ];

        $serviceMock
            ->expects($this->once())
            ->method('unassignFromContent')
            ->with(...$parameters);

        $decoratedService->unassignFromContent(...$parameters);
    }

    public function testAssignMultipleToContentDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(Content::class),
            [$this->createMock(TaxonomyEntry::class)],
            1,
        ];

        $serviceMock
            ->expects($this->once())
            ->method('assignMultipleToContent')
            ->with(...$parameters);

        $decoratedService->assignMultipleToContent(...$parameters);
    }

    public function testUnassignMultipleFromContentDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(Content::class),
            [$this->createMock(TaxonomyEntry::class)],
            1,
        ];

        $serviceMock
            ->expects($this->once())
            ->method('unassignMultipleFromContent')
            ->with(...$parameters);

        $decoratedService->unassignMultipleFromContent(...$parameters);
    }
}
