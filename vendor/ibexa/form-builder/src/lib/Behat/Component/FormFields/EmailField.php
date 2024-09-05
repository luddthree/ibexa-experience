<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class EmailField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom email');
        $this->fillField('Placeholder', 'Custom placeholder');
        $this->fillField('Help text', 'Custom help');
        $this->switchTab('Validation');
        $this->check('Required');
        $this->select('Regular expression pattern', 'Custom');
        $this->fillField('Pattern', '/[a-z\.]*@[a-z\.]*/');
    }

    public function getType(): string
    {
        return 'Email';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom email',
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

        $this->switchTab('Validation');

        Assert::assertTrue(
            $this->getCheckValue('Required'),
            'Wrong "Required" field value.'
        );
        Assert::assertEquals(
            'Custom',
            $this->getSelectValue('Regular expression pattern'),
            'Wrong "Regular expression pattern" field value.'
        );
        Assert::assertEquals(
            '/[a-z\.]*@[a-z\.]*/',
            $this->getFieldValue('Pattern'),
            'Wrong "Pattern" field value.'
        );
    }
}
