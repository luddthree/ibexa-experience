<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Tests\Form\Validator;

use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotEmptyUrlRedirectAction;
use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotEmptyUrlRedirectActionValidator;
use Symfony\Component\Form\FormInterface;

final class IsNotEmptyUrlRedirectActionValidatorTest extends AbstractActionValidatorTest
{
    protected function createValidator(): IsNotEmptyUrlRedirectActionValidator
    {
        return new IsNotEmptyUrlRedirectActionValidator();
    }

    public function testEmptyOnMessageActionThrowsException(): void
    {
        $constraint = new IsNotEmptyUrlRedirectAction();
        $form = $this->createMock(FormInterface::class);
        $attributeValue = [
            'action' => 'url',
            'location_id' => null,
            'url' => '',
            'message' => null,
        ];
        $data = $this->getSubmitButton($attributeValue);

        $form->method('getData')->willReturn($data);

        $this->setRoot($form);

        $this->validator->validate(null, $constraint);

        self::assertCount(1, $this->context->getViolations());
    }

    public function testNotEmptyOnMessageActionDoesNotThrowException(): void
    {
        $constraint = new IsNotEmptyUrlRedirectAction();
        $form = $this->createMock(FormInterface::class);

        $attributeValue = [
            'action' => 'url',
            'location_id' => null,
            'url' => 'https://ibexa.co',
            'message' => null,
        ];
        $data = $this->getSubmitButton($attributeValue);

        $form->method('getData')->willReturn($data);

        $this->setRoot($form);

        $this->validator->validate('https://ibexa.co', $constraint);

        $this->assertNoViolation();
    }
}
