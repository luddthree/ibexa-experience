<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\EventListener;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzRichTextFieldTypeSubscriber;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzDateFieldTypeSubscriber
 */
final class EzRichTextFieldTypeListenerTest extends TestCase
{
    public function testConvertKaliopEzDateValue(): void
    {
        foreach ($this->provideStubs() as $event) {
            $listener = new EzRichTextFieldTypeSubscriber();
            $listener->convertKaliopEzRichTextValue($event);
        }
    }

    /**
     * @return iterable<\Ibexa\Migration\Event\FieldValueFromHashEvent>
     */
    public function provideStubs(): iterable
    {
        $fieldTypeIdentifier = 'ezrichtext';
        $event = new FieldValueFromHashEvent($fieldTypeIdentifier, [], 'text');
        yield $event;
        self::assertSame(['xml' => 'text'], $event->getHash(), 'Strings are changed');

        $event = new FieldValueFromHashEvent($fieldTypeIdentifier, [], 0);
        yield $event;
        self::assertSame(['xml' => 0], $event->getHash(), 'Integers aren changed');

        $event = new FieldValueFromHashEvent($fieldTypeIdentifier, [], ['timestamp' => 0]);
        yield $event;
        self::assertSame(['timestamp' => 0], $event->getHash(), "Arrays aren't changed");

        $event = new FieldValueFromHashEvent($fieldTypeIdentifier, [], ['content' => 'text']);
        yield $event;
        self::assertSame(['xml' => 'text'], $event->getHash(), 'Arrays with "content" field are changed');

        $event = new FieldValueFromHashEvent('__any_other_field_type__', [], 0);
        yield $event;
        self::assertSame(0, $event->getHash(), 'Other field types remain unchanged');
    }
}

class_alias(EzRichTextFieldTypeListenerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzRichTextFieldTypeListenerTest');
