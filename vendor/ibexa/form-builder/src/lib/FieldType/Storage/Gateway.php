<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Storage;

use Ibexa\Contracts\Core\FieldType\StorageGateway;

abstract class Gateway extends StorageGateway
{
    /**
     * @param int $contentId
     * @param int $versionNo
     * @param int $contentFieldId
     * @param string $languageCode
     *
     * @return int
     */
    abstract public function insertForm(int $contentId, int $versionNo, int $contentFieldId, string $languageCode): int;

    /**
     * @param int $formId
     */
    abstract public function removeForm(int $formId): void;

    /**
     * @param int $formId
     * @param array $field
     *
     * @return int
     */
    abstract public function insertField(int $formId, array $field): int;

    /**
     * @param int $fieldId
     */
    abstract public function removeField(int $fieldId): void;

    /**
     * @param int $fieldId
     * @param array $attribute
     *
     * @return int
     */
    abstract public function insertAttribute(int $fieldId, array $attribute): int;

    /**
     * @param int $attributeId
     */
    abstract public function removeAttribute(int $attributeId): void;

    /**
     * @param int $fieldId
     */
    abstract public function removeAttributes(int $fieldId): void;

    /**
     * @param int $fieldId
     * @param array $validator
     *
     * @return int
     */
    abstract public function insertValidator(int $fieldId, array $validator): int;

    /**
     * @param int $validatorId
     */
    abstract public function removeValidator(int $validatorId): void;

    /**
     * @param int $fieldId
     */
    abstract public function removeValidators(int $fieldId): void;

    /**
     * @param int $formId
     *
     * @return array
     */
    abstract public function loadForm(int $formId): array;

    /**
     * @param int $contentId
     * @param int $versionNo
     * @param int $contentFieldId
     * @param string $languageCode
     *
     * @return array
     *
     * @throws \Ibexa\FormBuilder\Exception\FormNotFoundException
     */
    abstract public function loadFormByContentFieldId(int $contentId, int $versionNo, int $contentFieldId, string $languageCode): array;

    /**
     * @param int $formId
     *
     * @return array
     */
    abstract public function loadFormFields(int $formId): array;

    /**
     * @param int $fieldId
     *
     * @return array
     */
    abstract public function loadFieldAttributes(int $fieldId): array;

    /**
     * @param int $fieldId
     *
     * @return array
     */
    abstract public function loadFieldValidators(int $fieldId): array;

    /**
     * @param int $contentId
     * @param int $versionNo
     * @param array $languageCodes
     *
     * @return array
     *
     * @throws \Ibexa\FormBuilder\Exception\FormNotFoundException
     */
    abstract public function loadFormsByContent(int $contentId, int $versionNo, array $languageCodes): array;
}

class_alias(Gateway::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Storage\Gateway');
