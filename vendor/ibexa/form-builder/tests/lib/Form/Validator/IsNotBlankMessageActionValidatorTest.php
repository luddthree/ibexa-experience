<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FormBuilder\Tests\Form\Validator;

use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotBlankMessageAction;
use Ibexa\FormBuilder\Form\Validator\Constraints\IsNotBlankMessageActionValidator;
use Symfony\Component\Form\FormInterface;

final class IsNotBlankMessageActionValidatorTest extends AbstractActionValidatorTest
{
    protected function createValidator(): IsNotBlankMessageActionValidator
    {
        return new IsNotBlankMessageActionValidator();
    }

    public function testEmptyOnMessageActionThrowsException(): void
    {
        $constraint = new IsNotBlankMessageAction();
        $form = $this->createMock(FormInterface::class);

        $attributeValue = [
            'action' => 'message',
            'location_id' => null,
            'url' => null,
            'message' => '',
        ];
        $data = $this->getSubmitButton($attributeValue);

        $form->method('getData')->willReturn($data);

        $this->setRoot($form);

        $this->validator->validate('', $constraint);

        self::assertCount(1, $this->context->getViolations());
    }

    public function testNotEmptyOnMessageActionDoesNotThrowException(): void
    {
        $constraint = new IsNotBlankMessageAction();
        $form = $this->createMock(FormInterface::class);

        $attributeValue = [
            'action' => 'message',
            'location_id' => null,
            'url' => null,
            'message' => 'Test message',
        ];
        $data = $this->getSubmitButton($attributeValue);

        $form->method('getData')->willReturn($data);

        $this->setRoot($form);

        $this->validator->validate('test', $constraint);

        $this->assertNoViolation();
    }

    public function testEmptyOnDifferentActionDoesNotThrowException(): void
    {
        $constraint = new IsNotBlankMessageAction();
        $form = $this->createMock(FormInterface::class);

        $attributeValue = [
            'action' => 'url',
            'location_id' => null,
            'url' => null,
            'message' => '',
        ];
        $data = $this->getSubmitButton($attributeValue);

        $form->method('getData')->willReturn($data);

        $this->setRoot($form);

        $this->validator->validate('', $constraint);

        $this->assertNoViolation();
    }
}
