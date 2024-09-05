<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class CheckboxField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom checkbox');
        $this->fillField('Help text', 'Custom help');
        $this->switchTab('Validation');
        $this->check('Required');
    }

    public function getType(): string
    {
        return 'Checkbox';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals('Custom checkbox', $this->getFieldValue('Name'), 'Wrong "Name" field value.');
        Assert::assertEquals('Custom help', $this->getFieldValue('Help text'), 'Wrong "Help text" field value.');

        $this->switchTab('Validation');

        Assert::assertTrue($this->getCheckValue('Required'), 'Wrong "Required" field value.');
    }
}
