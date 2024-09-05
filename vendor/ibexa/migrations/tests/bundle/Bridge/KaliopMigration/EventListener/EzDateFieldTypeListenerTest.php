<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\EventListener;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzDateFieldTypeSubscriber;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzDateFieldTypeSubscriber
 */
final class EzDateFieldTypeListenerTest extends TestCase
{
    public function testConvertKaliopEzDateValue(): void
    {
        foreach ($this->provideStubs() as $event) {
            $listener = new EzDateFieldTypeSubscriber();
            $listener->convertKaliopEzDateValue($event);
        }
    }

    /**
     * @return iterable<\Ibexa\Migration\Event\FieldValueFromHashEvent>
     */
    public function provideStubs(): iterable
    {
        foreach (['ezdate', 'ezdatetime'] as $fieldTypeIdentifier) {
            $event = new FieldValueFromHashEvent($fieldTypeIdentifier, [], 0);
            yield $event;
            self::assertSame(['timestamp' => 0], $event->getHash());

            $event = new FieldValueFromHashEvent($fieldTypeIdentifier, [], ['timestamp' => 0]);
            yield $event;
            self::assertSame(['timestamp' => 0], $event->getHash());
        }

        $event = new FieldValueFromHashEvent('__any_other_field_type__', [], 0);
        yield $event;
        self::assertSame(0, $event->getHash(), 'Other field types remain unchanged');
    }
}

class_alias(EzDateFieldTypeListenerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzDateFieldTypeListenerTest');
