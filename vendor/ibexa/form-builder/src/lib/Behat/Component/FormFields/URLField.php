<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class URLField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom URL');
        $this->fillField('Placeholder', 'Custom placeholder');
        $this->fillField('Help text', 'Custom help');
        $this->fillField('Default value', 'Custom default');
        $this->switchTab('Validation');
        $this->check('Required');
    }

    public function getType(): string
    {
        return 'URL';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom URL',
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
    }
}
