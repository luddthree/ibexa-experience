<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\Value as BaseValue;
use Ibexa\FormBuilder\FieldType\Converter\FormConverter;
use Symfony\Contracts\Translation\TranslatorInterface;

class Type extends FieldType
{
    public const FIELD_TYPE_IDENTIFIER = 'ezform';

    /** @var \Ibexa\FormBuilder\FieldType\Converter\FormConverter */
    protected $converter;

    private FormFactory $formFactory;

    private TranslatorInterface $translator;

    public function __construct(
        FormConverter $converter,
        FormFactory $formFactory,
        TranslatorInterface $translator
    ) {
        $this->converter = $converter;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
    }

    public function getFieldTypeLabel(): string
    {
        return $this->translator->trans(/** @Desc("Form") */
            'ezform.name',
            [],
            'ibexa_fieldtypes'
        );
    }

    protected function createValueFromInput($inputValue)
    {
        if (\is_string($inputValue)) {
            $inputValue = new Value($this->converter->decode($inputValue));
        }

        return $inputValue;
    }

    public function getFieldTypeIdentifier()
    {
        return self::FIELD_TYPE_IDENTIFIER;
    }

    public function getName(ValueInterface $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return '';
    }

    public function getEmptyValue()
    {
        return new Value($this->converter->fromArray([
            'fields' => [],
        ]));
    }

    /**
     * @throws \Ibexa\FormBuilder\Exception\ValidatorConstraintMapperNotFoundException
     */
    public function fromHash($hash)
    {
        if ($hash === null) {
            return $this->getEmptyValue();
        }

        $formValue = $this->converter->fromArray($hash);
        $form = $this->formFactory->createForm($formValue);

        return new Value($formValue, $form);
    }

    public function toPersistenceValue(ValueInterface $value)
    {
        return new PersistenceValue([
            'data' => null,
            'externalData' => $this->toHash($value),
            'sortKey' => null,
        ]);
    }

    public function fromPersistenceValue(PersistenceValue $fieldValue)
    {
        if (empty($fieldValue->externalData)) {
            return $this->getEmptyValue();
        }

        return $this->fromHash($fieldValue->externalData);
    }

    protected function checkValueStructure(BaseValue $value)
    {
    }

    /**
     * @param \Ibexa\FormBuilder\FieldType\Value
     */
    public function toHash(ValueInterface $value)
    {
        return $this->converter->toArray($value->getFormValue());
    }

    public function isSingular()
    {
        return true;
    }
}

class_alias(Type::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Type');
