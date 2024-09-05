<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class CheckboxesField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom checkboxes');
        $this->fillField('Help', 'Custom help');
        $this->addOption('first option');
        $this->addOption('second option');
        $this->switchTab('Validation');
        $this->check('Required');
        $this->fillField('Minimum number of choices', '0');
        $this->fillField('Maximum number of choices', '1');
    }

    public function getType(): string
    {
        return 'Checkboxes';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom checkboxes',
            $this->getFieldValue('Name'),
            'Wrong "Name" field value.'
        );
        Assert::assertEquals(
            'Custom help',
            $this->getFieldValue('Help'),
            'Wrong "Help" field value.'
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
        Assert::assertEquals(
            '0',
            $this->getFieldValue('Minimum number of choices'),
            'Wrong "Minimum number of choices" field value.'
        );
        Assert::assertEquals(
            '1',
            $this->getFieldValue('Maximum number of choices'),
            'Wrong "Maximum number of choices" field value.'
        );
    }
}
