<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Service\Decorator;

use Ibexa\Contracts\Taxonomy\Service\Decorator\TaxonomyServiceDecorator;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Traversable;

final class TaxonomyServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_ENTRY_ID = 1;
    private const EXAMPLE_ENTRY_IDENTIFIER = 'test_entry';
    private const EXAMPLE_TAXONOMY_NAME = 'tags';
    private const EXAMPLE_CONTENT_ID = 2;

    protected function createDecorator(TaxonomyServiceInterface $service): TaxonomyServiceInterface
    {
        return new class($service) extends TaxonomyServiceDecorator {
        };
    }

    /**
     * @return \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createServiceMock(): MockObject
    {
        return $this->createMock(TaxonomyServiceInterface::class);
    }

    public function testLoadEntryByIdDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [self::EXAMPLE_ENTRY_ID];

        $serviceMock
            ->expects($this->once())
            ->method('loadEntryById')
            ->with(...$parameters)
            ->willReturn($this->createMock(TaxonomyEntry::class));

        $decoratedService->loadEntryById(...$parameters);
    }

    public function testLoadEntryByIdentifierDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [self::EXAMPLE_ENTRY_IDENTIFIER];

        $serviceMock
            ->expects($this->once())
            ->method('loadEntryByIdentifier')
            ->with(...$parameters)
            ->willReturn($this->createMock(TaxonomyEntry::class));

        $decoratedService->loadEntryByIdentifier(...$parameters);
    }

    public function testLoadRootEntryDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [self::EXAMPLE_TAXONOMY_NAME];

        $serviceMock
            ->expects($this->once())
            ->method('loadRootEntry')
            ->with(...$parameters)
            ->willReturn($this->createMock(TaxonomyEntry::class));

        $decoratedService->loadRootEntry(...$parameters);
    }

    public function testLoadEntryByContentIdDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [self::EXAMPLE_CONTENT_ID];

        $serviceMock
            ->expects($this->once())
            ->method('loadEntryByContentId')
            ->with(...$parameters)
            ->willReturn($this->createMock(TaxonomyEntry::class));

        $decoratedService->loadEntryByContentId(...$parameters);
    }

    public function testCreateEntryDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(TaxonomyEntryCreateStruct::class)];

        $serviceMock
            ->expects($this->once())
            ->method('createEntry')
            ->with(...$parameters)
            ->willReturn($this->createMock(TaxonomyEntry::class));

        $decoratedService->createEntry(...$parameters);
    }

    public function testUpdateEntryDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(TaxonomyEntry::class),
            $this->createMock(TaxonomyEntryUpdateStruct::class),
        ];

        $serviceMock
            ->expects($this->once())
            ->method('updateEntry')
            ->with(...$parameters)
            ->willReturn($this->createMock(TaxonomyEntry::class));

        $decoratedService->updateEntry(...$parameters);
    }

    public function testRemoveEntryDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(TaxonomyEntry::class)];

        $serviceMock
            ->expects($this->once())
            ->method('removeEntry')
            ->with(...$parameters);

        $decoratedService->removeEntry(...$parameters);
    }

    public function testMoveEntryDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(TaxonomyEntry::class),
            $this->createMock(TaxonomyEntry::class),
        ];

        $serviceMock
            ->expects($this->once())
            ->method('moveEntry')
            ->with(...$parameters);

        $decoratedService->moveEntry(...$parameters);
    }

    public function testMoveEntryRelativeToSiblingDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(TaxonomyEntry::class),
            $this->createMock(TaxonomyEntry::class),
            TaxonomyServiceInterface::MOVE_POSITION_NEXT,
        ];

        $serviceMock
            ->expects($this->once())
            ->method('moveEntryRelativeToSibling')
            ->with(...$parameters);

        $decoratedService->moveEntryRelativeToSibling(...$parameters);
    }

    public function testLoadAllEntriesDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            self::EXAMPLE_TAXONOMY_NAME,
            30,
            0,
        ];

        $serviceMock
            ->expects($this->once())
            ->method('loadAllEntries')
            ->with(...$parameters)
            ->willReturn($this->createMock(Traversable::class));

        $decoratedService->loadAllEntries(...$parameters);
    }

    public function testCountAllEntriesDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [self::EXAMPLE_TAXONOMY_NAME];

        $serviceMock
            ->expects($this->once())
            ->method('countAllEntries')
            ->with(...$parameters)
            ->willReturn(5);

        $decoratedService->countAllEntries(...$parameters);
    }

    public function testLoadEntryChildrenDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [
            $this->createMock(TaxonomyEntry::class),
            30,
            0,
        ];

        $serviceMock
            ->expects($this->once())
            ->method('loadEntryChildren')
            ->with(...$parameters)
            ->willReturn($this->createMock(Traversable::class));

        $decoratedService->loadEntryChildren(...$parameters);
    }

    public function testCountEntryChildrenDecorator(): void
    {
        $serviceMock = $this->createServiceMock();
        $decoratedService = $this->createDecorator($serviceMock);

        $parameters = [$this->createMock(TaxonomyEntry::class)];

        $serviceMock
            ->expects($this->once())
            ->method('countEntryChildren')
            ->with(...$parameters)
            ->willReturn(5);

        $decoratedService->countEntryChildren(...$parameters);
    }
}
