<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\EventListener;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzSelectionFieldTypeSubscriber;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzSelectionFieldTypeSubscriber
 */
final class EzSelectionFieldTypeListenerTest extends TestCase
{
    /**
     * @dataProvider providerForConvertKaliopEzSelectionValue
     *
     * @param array<string, mixed> $settings
     * @param mixed $hash
     * @param mixed $expectedHash
     */
    public function testConvertKaliopEzSelectionValueWhenEventIsForEzSelection(array $settings, $hash, $expectedHash): void
    {
        $event = new FieldValueFromHashEvent('ezselection', $settings, $hash);

        $listener = new EzSelectionFieldTypeSubscriber();
        $listener->convertKaliopEzSelectionValue($event);

        self::assertSame($expectedHash, $event->getHash());
    }

    /**
     * @return iterable<array{
     *      array<string, mixed>,
     *      mixed,
     *      array<mixed>
     * }>
     */
    public function providerForConvertKaliopEzSelectionValue(): iterable
    {
        yield [
            $settings = [],
            $hash = 1,
            $expectedHash = [1],
        ];

        yield [
            $settings = [
                'options' => [
                    0 => 'opt1',
                    17 => '__value__',
                    99 => 'xxx',
                ],
            ],
            $hash = '__value__',
            $expectedHash = [17],
        ];

        yield [
            $settings = [
                'options' => [
                    0 => 'opt1',
                    17 => '__value__',
                    99 => 'xxx',
                ],
            ],
            $hash = [
                '__value__',
                99,
                '__value_not_defined_in_settings__',
            ],
            $expectedHash = [17, 99, '__value_not_defined_in_settings__'],
        ];

        yield [
            $settings = [
                'options' => [
                    0 => 'opt1',
                    99 => 'xxx',
                ],
            ],
            $hash = '__value__',
            $expectedHash = ['__value__'],
        ];

        yield [
            $settings = [],
            $hash = '__value__',
            $expectedHash = ['__value__'],
        ];
    }

    public function testConvertKaliopEzSelectionValueWhenEventIsForOtherFieldTypeThanEzSelection(): void
    {
        $event = new FieldValueFromHashEvent('someezfieldtype', [], '__HASH__');

        $listener = new EzSelectionFieldTypeSubscriber();
        $listener->convertKaliopEzSelectionValue($event);

        self::assertSame('__HASH__', $event->getHash());
    }
}

class_alias(EzSelectionFieldTypeListenerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzSelectionFieldTypeListenerTest');
