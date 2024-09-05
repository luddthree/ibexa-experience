<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Service;

use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\HashFromFieldValueEvent;
use Ibexa\Migration\Event\MigrationEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class FieldTypeService implements FieldTypeServiceInterface
{
    /** @var \Ibexa\Core\FieldType\FieldTypeRegistry */
    private $fieldTypeRegistry;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        FieldTypeRegistry $fieldTypeRegistry,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->fieldTypeRegistry = $fieldTypeRegistry;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getHashFromFieldValue(Value $persistenceValue, string $fieldTypeIdentifier)
    {
        $hash = $this->getFieldType($fieldTypeIdentifier)->toHash($persistenceValue);

        $event = new HashFromFieldValueEvent($persistenceValue, $hash);
        $this->eventDispatcher->dispatch($event, MigrationEvents::AFTER_HASH_FROM_FIELD_VALUE);

        return $event->getHash();
    }

    public function getFieldValueFromHash($hash, string $fieldTypeIdentifier, array $fieldTypeSettings): Value
    {
        $event = new FieldValueFromHashEvent($fieldTypeIdentifier, $fieldTypeSettings, $hash);
        $this->eventDispatcher->dispatch($event, MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH);

        $hash = $event->getHash();
        $fieldType = $this->getFieldType($fieldTypeIdentifier);

        if (empty($hash)) {
            return $fieldType->getEmptyValue();
        }

        return $fieldType->fromHash($hash);
    }

    public function getFieldSettingsFromHash($fieldSettingsHash, string $fieldTypeIdentifier): ?array
    {
        $fieldType = $this->getFieldType($fieldTypeIdentifier);
        $fieldSettings = $fieldType->fieldSettingsFromHash($fieldSettingsHash);

        return $this->normalizeFieldSettings(
            $fieldSettings,
            $fieldTypeIdentifier,
            sprintf('%s::fieldSettingsFromHash', get_class($fieldType))
        );
    }

    public function getFieldSettingsToHash($fieldSettings, string $fieldTypeIdentifier): ?array
    {
        $fieldType = $this->getFieldType($fieldTypeIdentifier);
        $fieldSettingsHash = $fieldType->fieldSettingsToHash($fieldSettings);

        return $this->normalizeFieldSettings(
            $fieldSettingsHash,
            $fieldTypeIdentifier,
            sprintf('%s::fieldSettingsToHash', get_class($fieldType))
        );
    }

    private function getFieldType(string $fieldTypeIdentifier): FieldType
    {
        return $this->fieldTypeRegistry->getFieldType(
            $fieldTypeIdentifier
        );
    }

    /**
     * Provide compatibility between possible persistence value result and API requirements.
     *
     * @param mixed $fieldSettings
     *
     * @return array<mixed>|null
     */
    private function normalizeFieldSettings($fieldSettings, string $fieldTypeIdentifier, string $source): ?array
    {
        if (null !== $fieldSettings && !is_array($fieldSettings)) {
            trigger_deprecation(
                'ibexa/migrations',
                '4.5',
                sprintf(
                    'Returning non-hash field settings by %s for "%s" field type is deprecated ' .
                    'and will result in a fatal error in 5.0',
                    $source,
                    $fieldTypeIdentifier
                )
            );
            $fieldSettings = (array)$fieldSettings;
        }

        return $fieldSettings;
    }
}

class_alias(FieldTypeService::class, 'Ibexa\Platform\Migration\Service\FieldTypeService');
