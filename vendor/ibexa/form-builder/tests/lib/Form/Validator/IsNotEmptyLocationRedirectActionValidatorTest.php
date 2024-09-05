<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Tests\Form\Validator;

use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotEmptyLocationRedirectAction;
use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotEmptyLocationRedirectActionValidator;
use Symfony\Component\Form\FormInterface;

final class IsNotEmptyLocationRedirectActionValidatorTest extends AbstractActionValidatorTest
{
    protected function createValidator(): IsNotEmptyLocationRedirectActionValidator
    {
        return new IsNotEmptyLocationRedirectActionValidator();
    }

    public function testEmptyOnMessageActionThrowsException(): void
    {
        $constraint = new IsNotEmptyLocationRedirectAction();
        $form = $this->createMock(FormInterface::class);

        $attributeValue = [
            'action' => 'location_id',
            'location_id' => null,
            'url' => null,
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
        $constraint = new IsNotEmptyLocationRedirectAction();
        $form = $this->createMock(FormInterface::class);
        $attributeValue = [
            'action' => 'location_id',
            'location_id' => 2,
            'url' => null,
            'message' => null,
        ];
        $data = $this->getSubmitButton($attributeValue);

        $form->method('getData')->willReturn($data);

        $this->setRoot($form);

        $this->validator->validate(2, $constraint);

        $this->assertNoViolation();
    }
}
