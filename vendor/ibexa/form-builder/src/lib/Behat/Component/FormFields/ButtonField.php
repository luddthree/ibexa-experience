<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class ButtonField extends FormBuilderField
{
    protected function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [new VisibleCSSLocator('actionConfigField', '.ibexa-fb-form-field-config__actions .ibexa-fb-form-field-config__actions:not([hidden])')],
        );
    }

    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom button');
        $this->select('Action', 'Show a message');
        $this->fillField('Message to display', 'Submit success');
        $this->fillField('Notification email(s)', 'noreply@ibexa.co');
    }

    public function getType(): string
    {
        return 'button';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals(
            'Custom button',
            $this->getFieldValue('Name'),
            'Wrong "Name" field value.'
        );
        Assert::assertEquals(
            'Show a message',
            $this->getSelectValue('Action'),
            'Wrong "Action" field value.'
        );
        Assert::assertEquals(
            'Submit success',
            $this->getFieldValue('Message to display'),
            'Wrong "Message to display" field value.'
        );
        Assert::assertEquals(
            'noreply@ibexa.co',
            $this->getFieldValue('Notification email(s)'),
            'Wrong "Notification email(s)" field value.'
        );
    }
}
