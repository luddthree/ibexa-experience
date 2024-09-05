<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Behat\Component\FormFields;

interface FormFieldInterface
{
    public function getType(): string;

    public function setDefaultTestingConfiguration(): void;

    public function submitForm(): void;

    public function verifyDefaultTestingConfiguration(): void;

    public function verifyIsLoaded(): void;
}
