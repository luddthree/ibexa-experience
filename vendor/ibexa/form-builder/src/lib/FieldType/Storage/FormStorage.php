<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Storage;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Core\Persistence\Cache\ContentHandler;
use Ibexa\FormBuilder\Exception\FormNotFoundException;

/**
 * @property \Ibexa\FormBuilder\FieldType\Storage\Gateway $gateway
 */
class FormStorage extends GatewayBasedStorage
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\Handler */
    private $contentHandler;

    /**
     * @param \Ibexa\FormBuilder\FieldType\Storage\Gateway $gateway
     * @param \Ibexa\Core\Persistence\Cache\ContentHandler $contentHandler
     */
    public function __construct(
        Gateway $gateway,
        ContentHandler $contentHandler
    ) {
        parent::__construct($gateway);

        $this->contentHandler = $contentHandler;
    }

    /**
     * Allows custom field types to store data in an external source (e.g. another DB table).
     *
     * Stores value for $field in an external data source.
     * The whole {@link Ibexa\Contracts\Core\Persistence\Content\Field} object is passed and its value
     * is accessible through the {@link Ibexa\Contracts\Core\Persistence\Content\FieldValue} 'value' property.
     * This value holds the data filled by the user as a {@link Ibexa\Core\FieldType\Value} based object,
     * according to the field type (e.g. for TextLine, it will be a {@link Ibexa\Core\FieldType\TextLine\Value} object).
     *
     * $field->id = unique ID from the attribute tables (needs to be generated by
     * database back end on create, before the external data source may be
     * called from storing).
     *
     * The context array provides some context for the field handler about the
     * currently used storage engine.
     * The array should at least define 2 keys :
     *   - identifier (connection identifier)
     *   - connection (the connection handler)
     * For example, using Legacy storage engine, $context will be:
     *   - identifier = 'LegacyStorage'
     *   - connection = {@link \Ibexa\Core\Persistence\Doctrine\ConnectionHandler} object handler (for DB connection),
     *                  to be used accordingly to
     *                  {@link http://incubator.apache.org/zetacomponents/documentation/trunk/Database/tutorial.html ezcDatabase} usage
     *
     * This method might return true if $field needs to be updated after storage done here (to store a PK for instance).
     * In any other case, this method must not return anything (null).
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param array $context
     *
     * @return mixed null|true
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $versionNo = (int)$versionInfo->versionNo;
        $languageCode = $field->languageCode;
        $contentId = (int)$versionInfo->contentInfo->id;
        $contentFieldId = (int)$field->id;

        /** @var array $form */
        $form = $field->value->externalData;

        if (null === $form) {
            return null;
        }

        try {
            $existingForm = $this->gateway->loadFormByContentFieldId(
                $contentId,
                $versionNo,
                $contentFieldId,
                $languageCode
            );

            $this->removeForm((int)$existingForm['id']);
        } catch (FormNotFoundException $e) {
        }

        $this->insertForm((int)$contentId, $versionNo, $contentFieldId, $languageCode, $form);

        return false;
    }

    /**
     * Populates $field value property based on the external data.
     * $field->value is a {@link Ibexa\Contracts\Core\Persistence\Content\FieldValue} object.
     * This value holds the data as a {@link Ibexa\Core\FieldType\Value} based object,
     * according to the field type (e.g. for TextLine, it will be a {@link Ibexa\Core\FieldType\TextLine\Value} object).
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param array $context
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $languageCode = $field->languageCode;
        $versionNo = $versionInfo->versionNo;
        $contentId = $versionInfo->contentInfo->id;
        $contentFieldId = $field->id;

        try {
            $form = $this->gateway->loadFormByContentFieldId($contentId, $versionNo, $contentFieldId, $languageCode);
        } catch (FormNotFoundException $e) {
            return;
        }

        $form = [
            'id' => (int)$form['id'],
            'content_id' => $form['content_id'],
            'version_no' => $form['version_no'],
            'content_field_id' => $form['content_field_id'],
            'language_code' => $form['language_code'],
            'fields' => [],
        ];

        foreach ($this->gateway->loadFormFields($form['id']) as $formField) {
            $form['fields'][$formField['id']] = [
                'id' => $formField['id'],
                'identifier' => $formField['identifier'],
                'name' => $formField['name'],
                'attributes' => $this->gateway->loadFieldAttributes((int)$formField['id']),
                'validators' => $this->gateway->loadFieldValidators((int)$formField['id']),
            ];
        }

        $field->value->externalData = $form;
    }

    /**
     * Deletes field data for all $fieldIds in the version identified by
     * $versionInfo.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param array $fieldIds Array of field IDs
     * @param array $context
     *
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        $content = $this->contentHandler->load($versionInfo->contentInfo->id, $versionInfo->versionNo);
        $fields = $this->findFields($content, $fieldIds);
        $languageCodes = array_unique(array_column($fields, 'languageCode'));

        try {
            $forms = $this->gateway->loadFormsByContent(
                $versionInfo->contentInfo->id,
                $versionInfo->versionNo,
                $languageCodes
            );

            foreach ($forms as $form) {
                $this->removeForm((int)$form['id']);
            }
        } catch (FormNotFoundException $exception) {
        }
    }

    /**
     * Checks if field type has external data to deal with.
     *
     * @return bool
     */
    public function hasFieldData()
    {
        return true;
    }

    /**
     * Get index data for external data for search backend.
     *
     * @deprecated Use Ibexa\Contracts\Core\FieldType\Indexable
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\VersionInfo $versionInfo
     * @param \Ibexa\Contracts\Core\Persistence\Content\Field $field
     * @param array $context
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return null;
    }

    /**
     * @param int $contentId
     * @param int $versionNo
     * @param int $contentFieldId
     * @param string $languageCode
     * @param array $form
     *
     * @return int|null
     */
    private function insertForm(int $contentId, int $versionNo, int $contentFieldId, string $languageCode, array $form): ?int
    {
        $formId = $this->gateway->insertForm($contentId, $versionNo, $contentFieldId, $languageCode);

        foreach ($form['fields'] as $field) {
            $fieldId = $this->gateway->insertField($formId, $field);

            foreach ($field['attributes'] as $attribute) {
                $this->gateway->insertAttribute($fieldId, $attribute);
            }

            foreach ($field['validators'] as $validator) {
                $this->gateway->insertValidator($fieldId, $validator);
            }
        }

        return $formId;
    }

    /**
     * @param int $formId
     */
    private function removeForm(int $formId)
    {
        $fields = $this->gateway->loadFormFields($formId);

        foreach ($fields as $field) {
            $this->gateway->removeAttributes((int)$field['id']);
            $this->gateway->removeValidators((int)$field['id']);
            $this->gateway->removeField((int)$field['id']);
        }

        $this->gateway->removeForm($formId);
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content $content
     * @param int[] $fieldIds
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Field[]
     */
    private function findFields(Content $content, array $fieldIds): array
    {
        $fields = [];

        foreach ($content->fields as $field) {
            if (\in_array($field->id, $fieldIds)) {
                $fields[] = $field;
            }
        }

        return $fields;
    }
}

class_alias(FormStorage::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Storage\FormStorage');
