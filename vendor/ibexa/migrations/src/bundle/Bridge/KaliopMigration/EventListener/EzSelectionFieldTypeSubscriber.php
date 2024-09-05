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

final class EzSelectionFieldTypeSubscriber implements EventSubscriberInterface
{
    public function convertKaliopEzSelectionValue(FieldValueFromHashEvent $event): void
    {
        $hash = $event->getHash();

        if ('ezselection' !== $event->getFieldTypeIdentifier()) {
            return;
        }

        if (is_array($hash)) {
            $settings = $event->getFieldTypeSettings();

            $hash = array_map(function ($hash) use ($settings) {
                if (is_string($hash)) {
                    $index = $this->convertValueToIndex($settings, $hash);

                    return $index ?? $hash;
                }

                return $hash;
            }, $hash);

            $event->setHash($hash);

            return;
        }

        if (is_string($hash)) {
            $settings = $event->getFieldTypeSettings();
            $index = $this->convertValueToIndex($settings, $hash);

            $hash = $index ?? $hash;
        }

        $event->setHash([$hash]);
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function convertValueToIndex(array $settings, string $hash): ?int
    {
        $index = array_search($hash, $settings['options'] ?? [], true);
        if (false !== $index) {
            return $index;
        }

        return null;
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents()
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                'convertKaliopEzSelectionValue',
                -1,
            ],
        ];
    }
}

class_alias(EzSelectionFieldTypeSubscriber::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzSelectionFieldTypeSubscriber');
