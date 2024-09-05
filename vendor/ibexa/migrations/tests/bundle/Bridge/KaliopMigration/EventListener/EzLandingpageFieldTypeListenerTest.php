<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\EventListener;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzLandingpageFieldTypeSubscriber;
use Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzSelectionFieldTypeSubscriber;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzSelectionFieldTypeSubscriber
 */
final class EzLandingpageFieldTypeListenerTest extends TestCase
{
    /**
     * @dataProvider providerForconvertKaliopEzLandingpageValue
     *
     * @param mixed $hash
     * @param array<string, mixed> $expectedHash
     */
    public function testConvertKaliopEzLandingpageValueWhenEventIsForEzLandingpage($hash, $expectedHash): void
    {
        $event = new FieldValueFromHashEvent('ezlandingpage', [], $hash);

        $listener = new EzLandingpageFieldTypeSubscriber();
        $listener->convertKaliopEzLandingpageValue($event);

        self::assertSame($expectedHash, $event->getHash());
    }

    /**
     * @return iterable<array{
     *      mixed,
     *      array<mixed>
     * }>
     */
    public function providerForconvertKaliopEzLandingpageValue(): iterable
    {
        yield [
            $hash = '{"test": 1}',
            $expectedHash = [
                'test' => 1,
            ],
        ];

        yield [
            $hash = ['test' => 1],
            $expectedHash = [
                'test' => 1,
            ],
        ];

        yield [
            $hash = '',
            $expectedHash = [],
        ];

        yield [
            $hash = null,
            $expectedHash = [],
        ];
    }

    public function testConvertKaliopEzLandingpageValueWhenValueHasInvalidJson(): void
    {
        $hash = '{test: 1';
        $event = new FieldValueFromHashEvent('ezlandingpage', [], $hash);

        $listener = new EzLandingpageFieldTypeSubscriber();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Value of field with type `ezlandingpage` has invalid JSON');
        $listener->convertKaliopEzLandingpageValue($event);
    }

    public function testConvertKaliopEzLandingpageValueWhenEventIsForOtherFieldTypeThanEzLandingpage(): void
    {
        $event = new FieldValueFromHashEvent('someezfieldtype', [], '__HASH__');

        $listener = new EzSelectionFieldTypeSubscriber();
        $listener->convertKaliopEzSelectionValue($event);

        self::assertSame('__HASH__', $event->getHash());
    }
}

class_alias(EzLandingpageFieldTypeListenerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzLandingpageFieldTypeListenerTest');
