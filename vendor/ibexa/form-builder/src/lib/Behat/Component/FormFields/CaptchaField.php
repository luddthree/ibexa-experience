<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

use PHPUnit\Framework\Assert;

class CaptchaField extends FormBuilderField
{
    public function setDefaultTestingConfiguration(): void
    {
        $this->fillField('Name', 'Custom captcha');
        $this->fillField('Help text', 'Custom help text');
    }

    public function getType(): string
    {
        return 'Captcha';
    }

    public function verifyDefaultTestingConfiguration(): void
    {
        Assert::assertEquals('Custom captcha', $this->getFieldValue('Name'), 'Wrong "Name" field value.');
        Assert::assertEquals('Custom help text', $this->getFieldValue('Help text'), 'Wrong "Help text" field value.');
    }
}
