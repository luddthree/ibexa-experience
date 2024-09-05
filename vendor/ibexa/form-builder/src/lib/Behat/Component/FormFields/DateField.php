<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class DateField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom date');
        $this->fillField('Placeholder', 'Custom placeholder');
        $this->fillField('Help text', 'Custom help');
        $this->select('Date format', 'dd/mm/yyyy');
        $this->check('Current date as default value');
        $this->switchTab('Validation');
        $this->check('Required');
    }

    public function getType(): string
    {
        return 'Date';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom date',
            $this->getFieldValue('Name'),
            'Wrong "Name" field value.'
        );
        Assert::assertEquals(
            'Custom placeholder',
            $this->getFieldValue('Placeholder'),
            'Wrong "Placeholder" field value.'
        );
        Assert::assertEquals(
            'Custom help',
            $this->getFieldValue('Help text'),
            'Wrong "Help text" field value.'
        );
        Assert::assertEquals(
            'dd/mm/yyyy',
            $this->getSelectValue('Date format'),
            'Wrong "Date format" field value.'
        );

        $this->switchTab('Validation');

        Assert::assertTrue(
            $this->getCheckValue('Required'),
            'Wrong "Required" field value.'
        );
    }
}
