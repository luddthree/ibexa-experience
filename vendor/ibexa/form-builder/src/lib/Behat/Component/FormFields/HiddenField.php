<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use Ibexa\Behat\Browser\Element\Condition\ElementHasTextCondition;
use PHPUnit\Framework\Assert;

class HiddenField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $name = 'Custom hidden';
        $this->fillField('Name', $name);
        $this->getHTMLPage()->find($this->getLocator('configPopupTitle'))->click();
        $this->getHTMLPage()->setTimeout(5)->waitUntilCondition(
            new ElementHasTextCondition(
                $this->getHTMLPage(),
                $this->getLocator('configPopupTitle'),
                $name
            )
        );
        $this->fillField('Default value', 'Custom default');
    }

    public function getType(): string
    {
        return 'Hidden field';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals('Custom hidden', $this->getFieldValue('Name'), 'Wrong "Name" field value.');
        Assert::assertEquals('Custom default', $this->getFieldValue('Default value'), 'Wrong "Default value" field value.');
    }
}
