<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\EventListener;

use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\MigrationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EzDateFieldTypeSubscriber implements EventSubscriberInterface
{
    public function convertKaliopEzDateValue(FieldValueFromHashEvent $event): void
    {
        $hash = $event->getHash();
        $fieldTypeIdentifier = $event->getFieldTypeIdentifier();

        if (!in_array($fieldTypeIdentifier, ['ezdate', 'ezdatetime'], true)) {
            return;
        }

        if (!is_int($hash)) {
            return;
        }

        $event->setHash([
            'timestamp' => $hash,
        ]);
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents()
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                'convertKaliopEzDateValue',
                -1,
            ],
        ];
    }
}

class_alias(EzDateFieldTypeSubscriber::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzDateFieldTypeSubscriber');
