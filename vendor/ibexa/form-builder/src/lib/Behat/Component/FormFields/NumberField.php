<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class NumberField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom number');
        $this->fillField('Placeholder', 'Custom placeholder');
        $this->fillField('Help text', 'Custom help');
        $this->fillField('Default value', 'Custom default');
        $this->switchTab('Validation');
        $this->check('Required');
        $this->fillField('Minimum value', '1');
        $this->fillField('Maximum value', '10');
        $this->select('Regular expression pattern', 'Custom');
        $this->fillField('Pattern', '/[0-9]*/');
    }

    public function getType(): string
    {
        return 'Number';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom number',
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
            'Custom default',
            $this->getFieldValue('Default value'),
            'Wrong "Default value" field value.'
        );

        $this->switchTab('Validation');

        Assert::assertTrue(
            $this->getCheckValue('Required'),
            'Wrong "Required" field value.'
        );
        Assert::assertEquals(
            '1',
            $this->getFieldValue('Minimum value'),
            'Wrong "Minimum value" field value.'
        );
        Assert::assertEquals(
            '10',
            $this->getFieldValue('Maximum value'),
            'Wrong "Maximum value" field value.'
        );
        Assert::assertEquals(
            'Custom',
            $this->getSelectValue('Regular expression pattern'),
            'Wrong "Regular expression pattern" field value.'
        );
        Assert::assertEquals(
            '/[0-9]*/',
            $this->getFieldValue('Pattern'),
            'Wrong "Pattern" field value.'
        );
    }
}
