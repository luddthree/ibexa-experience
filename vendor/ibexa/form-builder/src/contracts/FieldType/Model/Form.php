<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FormBuilder\FieldType\Model;

use Ibexa\FormBuilder\Exception\FormFieldNotFoundException;

class Form
{
    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Field[] */
    private $fields;

    /** @var int|null */
    private $contentId;

    /** @var int|null */
    private $contentFieldId;

    /** @var string|null */
    private $languageCode;

    /**
     * @param int|null $contentId
     * @param int|null $contentFieldId
     * @param string|null $languageCode
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field[] $fields
     */
    public function __construct(
        int $contentId = null,
        int $contentFieldId = null,
        string $languageCode = null,
        array $fields = []
    ) {
        $this->contentId = $contentId;
        $this->contentFieldId = $contentFieldId;
        $this->languageCode = $languageCode;
        $this->fields = $fields;
    }

    /**
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @param string $id
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Field
     *
     * @throws \Ibexa\FormBuilder\Exception\FormFieldNotFoundException
     */
    public function getFieldById(string $id): Field
    {
        foreach ($this->getFields() as $field) {
            if ($field->getId() === $id) {
                return $field;
            }
        }

        throw new FormFieldNotFoundException($id);
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Field
     *
     * @throws \Ibexa\FormBuilder\Exception\FormFieldNotFoundException
     */
    public function getFieldByIdentifier(string $identifier): Field
    {
        foreach ($this->getFields() as $field) {
            if ($field->getIdentifier() === $identifier) {
                return $field;
            }
        }

        throw new FormFieldNotFoundException($identifier);
    }

    /**
     * @return int|null
     */
    public function getContentId(): ?int
    {
        return $this->contentId;
    }

    /**
     * @param int|null $contentId
     */
    public function setContentId(?int $contentId): void
    {
        $this->contentId = $contentId;
    }

    /**
     * @return int|null
     */
    public function getContentFieldId(): ?int
    {
        return $this->contentFieldId;
    }

    /**
     * @param int|null $contentFieldId
     */
    public function setContentFieldId(?int $contentFieldId): void
    {
        $this->contentFieldId = $contentFieldId;
    }

    /**
     * @return string|null
     */
    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    /**
     * @param string|null $languageCode
     */
    public function setLanguageCode(?string $languageCode): void
    {
        $this->languageCode = $languageCode;
    }
}

class_alias(Form::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Model\Form');
