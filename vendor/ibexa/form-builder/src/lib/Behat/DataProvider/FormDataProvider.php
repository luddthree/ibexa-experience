<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Behat\DataProvider;

use Ibexa\Behat\API\ContentData\FieldTypeData\FieldTypeDataProviderInterface;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Validator;
use Ibexa\FormBuilder\FieldType\Type;
use Ibexa\FormBuilder\FieldType\Value;

class FormDataProvider implements FieldTypeDataProviderInterface
{
    /** @var \Ibexa\FormBuilder\FieldType\Type */
    private $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function supports(string $fieldTypeIdentifier): bool
    {
        return $fieldTypeIdentifier === Type::FIELD_TYPE_IDENTIFIER;
    }

    public function generateData(string $contentTypeIdentifier, string $fieldIdentifier, string $language = 'eng-GB')
    {
        return new Value($this->createForm('Test'));
    }

    public function parseFromString(string $value)
    {
        return new Value($this->createForm($value));
    }

    private function createForm($name): Form
    {
        $form = $this->type->getEmptyValue()->getFormValue();
        $form->setFields([
            $this->getSimpleField($name),
            $this->getSubmitButton(),
        ]);

        return $form;
    }

    private function getSimpleField(string $name): Field
    {
        return new Field(
            $this->getID(),
            'single_line',
            $name,
            [
                new Attribute('placeholder', ''),
                new Attribute('help', 'Enter your value.'),
                new Attribute('default_value', ''),
            ],
            [
                new Validator('required'),
                new Validator('min_length'),
                new Validator('max_length'),
                new Validator('regex'),
            ]
        );
    }

    private function getSubmitButton(): Field
    {
        return new Field(
            $this->getID(),
            'button',
            'Submit',
            [
                new Attribute('action', '{"action":"message","location_id":null,"url":null,"message":"Thank you for your submission."}'),
                new Attribute('notification_email'),
            ],
            []
        );
    }

    private function getID(): string
    {
        return str_replace('.', '', uniqid('', true));
    }
}
