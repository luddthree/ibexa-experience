<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Service\Event;

use Ibexa\Contracts\Taxonomy\Event\BeforeCreateTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\BeforeMoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\BeforeMoveTaxonomyEntryRelativeToSiblingEvent;
use Ibexa\Contracts\Taxonomy\Event\BeforeRemoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\BeforeUpdateTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\CreateTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\MoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\MoveTaxonomyEntryRelativeToSiblingEvent;
use Ibexa\Contracts\Taxonomy\Event\RemoveTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Event\UpdateTaxonomyEntryEvent;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use Ibexa\Taxonomy\Service\Event;
use Ibexa\Tests\Core\Event\AbstractServiceTest;

final class TaxonomyServiceTest extends AbstractServiceTest
{
    /**
     * @dataProvider provideForTestEventsForMethodWithReturnValue
     *
     * @param array<mixed> $parameters
     * @param mixed $returnValue
     */
    public function testEventsForMethodWithReturnValue(
        string $beforeEventClass,
        string $afterEventClass,
        string $testedMethod,
        array $parameters,
        $returnValue
    ): void {
        $traceableEventDispatcher = $this->getEventDispatcher(
            $beforeEventClass,
            $afterEventClass
        );

        $innerServiceMock = $this->createMock(TaxonomyServiceInterface::class);
        $innerServiceMock
            ->expects($this->once())
            ->method($testedMethod)
            ->with(...$parameters)
            ->willReturn($returnValue);

        $service = new Event\TaxonomyService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->{$testedMethod}(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($returnValue, $result);
        $this->assertSame($calledListeners, [
            [$beforeEventClass, 0],
            [$afterEventClass, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    /**
     * @dataProvider provideForTestEventsForMethodWithNoReturnValue
     *
     * @param array<mixed> $parameters
     */
    public function testEventsForMethodWithNoReturnValue(
        string $beforeEventClass,
        string $afterEventClass,
        string $testedMethod,
        array $parameters
    ): void {
        $traceableEventDispatcher = $this->getEventDispatcher(
            $beforeEventClass,
            $afterEventClass
        );

        $innerServiceMock = $this->createMock(TaxonomyServiceInterface::class);
        $innerServiceMock
            ->expects($this->once())
            ->method($testedMethod)
            ->with(...$parameters);

        $service = new Event\TaxonomyService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->{$testedMethod}(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($calledListeners, [
            [$beforeEventClass, 0],
            [$afterEventClass, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    /**
     * @dataProvider provideForTestReturnResultInBeforeEvent
     *
     * @param array<mixed> $parameters
     * @param mixed $returnValueSetByListener
     */
    public function testReturnResultInBeforeEvent(
        string $beforeEventClass,
        string $afterEventClass,
        string $testedMethod,
        array $parameters,
        string $setMethod,
        $returnValueSetByListener
    ): void {
        $traceableEventDispatcher = $this->getEventDispatcher(
            $beforeEventClass,
            $afterEventClass
        );

        $innerServiceMock = $this->createMock(TaxonomyServiceInterface::class);
        $innerServiceMock
            ->expects($this->never())
            ->method($testedMethod);

        $traceableEventDispatcher->addListener(
            $beforeEventClass,
            static function ($event) use ($setMethod, $returnValueSetByListener): void {
                $event->{$setMethod}($returnValueSetByListener);
            },
            10
        );

        $service = new Event\TaxonomyService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->{$testedMethod}(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());

        $this->assertSame($returnValueSetByListener, $result);
        $this->assertSame($calledListeners, [
            [$beforeEventClass, 10],
            [$beforeEventClass, 0],
            [$afterEventClass, 0],
        ]);
        $this->assertSame([], $traceableEventDispatcher->getNotCalledListeners());
    }

    /**
     * @dataProvider provideForTestStopPropagationInBeforeEvent
     *
     * @param array<mixed> $parameters
     * @param mixed $returnValueSetByListener
     */
    public function testStopPropagationInBeforeEvent(
        string $beforeEventClass,
        string $afterEventClass,
        string $testedMethod,
        array $parameters,
        string $setMethod,
        $returnValueSetByListener
    ): void {
        $traceableEventDispatcher = $this->getEventDispatcher(
            $beforeEventClass,
            $afterEventClass
        );

        $innerServiceMock = $this->createMock(TaxonomyServiceInterface::class);
        $innerServiceMock
            ->expects($this->never())
            ->method($testedMethod);

        $traceableEventDispatcher->addListener(
            $beforeEventClass,
            static function ($event) use ($setMethod, $returnValueSetByListener): void {
                $event->{$setMethod}($returnValueSetByListener);
                $event->stopPropagation();
            },
            10
        );

        $service = new Event\TaxonomyService($innerServiceMock, $traceableEventDispatcher);
        $result = $service->{$testedMethod}(...$parameters);

        $calledListeners = $this->getListenersStack($traceableEventDispatcher->getCalledListeners());
        $notCalledListeners = $this->getListenersStack($traceableEventDispatcher->getNotCalledListeners());

        $this->assertSame($returnValueSetByListener, $result);
        $this->assertSame($calledListeners, [
            [$beforeEventClass, 10],
        ]);
        $this->assertSame($notCalledListeners, [
            [$beforeEventClass, 0],
            [$afterEventClass, 0],
        ]);
    }

    /**
     * @return array<array{
     *     string,
     *     string,
     *     string,
     *     array<mixed>,
     *     mixed,
     * }>
     */
    public function provideForTestEventsForMethodWithReturnValue(): array
    {
        return [
            [
                BeforeCreateTaxonomyEntryEvent::class,
                CreateTaxonomyEntryEvent::class,
                'createEntry',
                [
                    $this->createMock(TaxonomyEntryCreateStruct::class),
                ],
                $this->createMock(TaxonomyEntry::class),
            ],
            [
                BeforeUpdateTaxonomyEntryEvent::class,
                UpdateTaxonomyEntryEvent::class,
                'updateEntry',
                [
                    $this->createMock(TaxonomyEntry::class),
                    $this->createMock(TaxonomyEntryUpdateStruct::class),
                ],
                $this->createMock(TaxonomyEntry::class),
            ],
        ];
    }

    /**
     * @return array<array{
     *     string,
     *     string,
     *     string,
     *     array<mixed>,
     * }>
     */
    public function provideForTestEventsForMethodWithNoReturnValue(): array
    {
        return [
            [
                BeforeRemoveTaxonomyEntryEvent::class,
                RemoveTaxonomyEntryEvent::class,
                'removeEntry',
                [
                    $this->createMock(TaxonomyEntry::class),
                ],
            ],
            [
                BeforeMoveTaxonomyEntryRelativeToSiblingEvent::class,
                MoveTaxonomyEntryRelativeToSiblingEvent::class,
                'moveEntryRelativeToSibling',
                [
                    $this->createMock(TaxonomyEntry::class),
                    $this->createMock(TaxonomyEntry::class),
                    TaxonomyServiceInterface::MOVE_POSITION_NEXT,
                ],
            ],
            [
                BeforeMoveTaxonomyEntryEvent::class,
                MoveTaxonomyEntryEvent::class,
                'moveEntry',
                [
                    $this->createMock(TaxonomyEntry::class),
                    $this->createMock(TaxonomyEntry::class),
                ],
            ],
        ];
    }

    /**
     * @return array<array{
     *     string,
     *     string,
     *     string,
     *     array<mixed>,
     *     string,
     *     mixed,
     * }>
     */
    public function provideForTestReturnResultInBeforeEvent(): array
    {
        return [
            [
                BeforeCreateTaxonomyEntryEvent::class,
                CreateTaxonomyEntryEvent::class,
                'createEntry',
                [
                    $this->createMock(TaxonomyEntryCreateStruct::class),
                ],
                'setTaxonomyEntry',
                $this->createMock(TaxonomyEntry::class),
            ],
            [
                BeforeUpdateTaxonomyEntryEvent::class,
                UpdateTaxonomyEntryEvent::class,
                'updateEntry',
                [
                    $this->createMock(TaxonomyEntry::class),
                    $this->createMock(TaxonomyEntryUpdateStruct::class),
                ],
                'setUpdatedTaxonomyEntry',
                $this->createMock(TaxonomyEntry::class),
            ],
        ];
    }

    /**
     * @return array<array{
     *     string,
     *     string,
     *     string,
     *     array<mixed>,
     *     string,
     *     mixed,
     * }>
     */
    public function provideForTestStopPropagationInBeforeEvent(): array
    {
        return [
            [
                BeforeCreateTaxonomyEntryEvent::class,
                CreateTaxonomyEntryEvent::class,
                'createEntry',
                [
                    $this->createMock(TaxonomyEntryCreateStruct::class),
                ],
                'setTaxonomyEntry',
                $this->createMock(TaxonomyEntry::class),
            ],
            [
                BeforeUpdateTaxonomyEntryEvent::class,
                UpdateTaxonomyEntryEvent::class,
                'updateEntry',
                [
                    $this->createMock(TaxonomyEntry::class),
                    $this->createMock(TaxonomyEntryUpdateStruct::class),
                ],
                'setUpdatedTaxonomyEntry',
                $this->createMock(TaxonomyEntry::class),
            ],
        ];
    }
}
