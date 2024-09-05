<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class DropdownField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom dropdown');
        $this->fillField('Help text', 'Custom help');
        $this->addOption('first option');
        $this->addOption('second option');
        $this->switchTab('Validation');
        $this->check('Required');
    }

    public function getType(): string
    {
        return 'Dropdown';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom dropdown',
            $this->getFieldValue('Name'),
            'Wrong "Name" field value.'
        );
        Assert::assertEquals(
            'Custom help',
            $this->getFieldValue('Help text'),
            'Wrong "Help text" field value.'
        );
        Assert::assertContains(
            'first option',
            $this->getOptions(),
            'There is no "first option" option in field.'
        );
        Assert::assertContains(
            'second option',
            $this->getOptions(),
            'There is no "second option" option in field.'
        );

        $this->switchTab('Validation');

        Assert::assertTrue(
            $this->getCheckValue('Required'),
            'Wrong "Required" field value.'
        );
    }
}
