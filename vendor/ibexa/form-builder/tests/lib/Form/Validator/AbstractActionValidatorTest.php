<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Tests\Form\Validator;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

abstract class AbstractActionValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @param array<string, mixed> $attributeValue
     */
    protected function getSubmitButton(array $attributeValue): Field
    {
        $encodedAttributeValue = json_encode($attributeValue);

        return new Field(
            str_replace('.', '', uniqid('', true)),
            'button',
            'Submit',
            [
                new Attribute(
                    'action',
                    is_string($encodedAttributeValue) ? $encodedAttributeValue : '',
                ),
                new Attribute('notification_email'),
            ],
        );
    }
}
