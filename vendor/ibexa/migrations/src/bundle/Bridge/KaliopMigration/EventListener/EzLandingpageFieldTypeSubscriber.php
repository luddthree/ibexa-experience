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

final class EzLandingpageFieldTypeSubscriber implements EventSubscriberInterface
{
    public function convertKaliopEzLandingpageValue(FieldValueFromHashEvent $event): void
    {
        $fieldTypeIdentifier = $event->getFieldTypeIdentifier();
        if ('ezlandingpage' !== $fieldTypeIdentifier) {
            return;
        }

        $hash = $event->getHash();
        if (empty($hash)) {
            $event->setHash([]);

            return;
        }

        if (is_array($hash)) {
            return;
        }

        try {
            $hash = \json_decode($hash, true, 512, JSON_THROW_ON_ERROR);
            $event->setHash($hash);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Value of field with type `ezlandingpage` has invalid JSON', $e->getCode(), $e);
        }
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents()
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                'convertKaliopEzLandingpageValue',
                -1,
            ],
        ];
    }
}

class_alias(EzLandingpageFieldTypeSubscriber::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzLandingpageFieldTypeSubscriber');
