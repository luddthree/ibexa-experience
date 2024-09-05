<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Service;

use Ibexa\Contracts\Core\FieldType\Value;

interface FieldTypeServiceInterface
{
    /**
     * @return array<scalar|null>|scalar|null
     */
    public function getHashFromFieldValue(Value $persistenceValue, string $fieldTypeIdentifier);

    /**
     * @param array<scalar|null>|scalar|null $hash
     * @param array<string, mixed> $fieldTypeSettings
     */
    public function getFieldValueFromHash($hash, string $fieldTypeIdentifier, array $fieldTypeSettings): Value;

    /**
     * @param array<mixed>|scalar|null $fieldSettingsHash
     *
     * @return array<mixed>|null
     */
    public function getFieldSettingsFromHash($fieldSettingsHash, string $fieldTypeIdentifier): ?array;

    /**
     * @param mixed $fieldSettings
     *
     * @return array<mixed>|null
     */
    public function getFieldSettingsToHash($fieldSettings, string $fieldTypeIdentifier): ?array;
}

class_alias(FieldTypeServiceInterface::class, 'Ibexa\Platform\Migration\Service\FieldTypeServiceInterface');
