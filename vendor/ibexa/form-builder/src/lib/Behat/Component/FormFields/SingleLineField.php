<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class SingleLineField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom single line');
        $this->fillField('Placeholder', 'Custom placeholder');
        $this->fillField('Help text', 'Custom help');
        $this->fillField('Default value', 'Custom default');
        $this->switchTab('Validation');
        $this->check('Required');
        $this->fillField('Minimum length', '10');
        $this->fillField('Maximum length', '100');
        $this->select('Regular expression pattern', 'Alphanumeric');
    }

    public function getType(): string
    {
        return 'single line input';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom single line',
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
            '10',
            $this->getFieldValue('Minimum length'),
            'Wrong "Minimum length" field value.'
        );
        Assert::assertEquals(
            '100',
            $this->getFieldValue('Maximum length'),
            'Wrong "Maximum length" field value.'
        );
        Assert::assertEquals(
            'Alphanumeric',
            $this->getSelectValue('Regular expression pattern'),
            'Wrong "Regular expression pattern" field value.'
        );
    }
}
