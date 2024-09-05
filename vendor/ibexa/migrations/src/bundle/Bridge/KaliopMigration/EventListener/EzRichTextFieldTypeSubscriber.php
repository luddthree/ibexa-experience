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

final class EzRichTextFieldTypeSubscriber implements EventSubscriberInterface
{
    public function convertKaliopEzRichTextValue(FieldValueFromHashEvent $event): void
    {
        $hash = $event->getHash();

        if ('ezrichtext' !== $event->getFieldTypeIdentifier()) {
            return;
        }

        if (is_array($hash) && isset($hash['content'])) {
            $event->setHash(['xml' => $hash['content']]);

            return;
        }

        if (is_scalar($hash)) {
            $event->setHash(['xml' => $hash]);

            return;
        }
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function getSubscribedEvents()
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                'convertKaliopEzRichTextValue',
                -1,
            ],
        ];
    }
}

class_alias(EzRichTextFieldTypeSubscriber::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\EventListener\EzRichTextFieldTypeSubscriber');
