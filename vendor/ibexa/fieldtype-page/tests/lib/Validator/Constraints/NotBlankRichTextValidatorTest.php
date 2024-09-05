<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Validator\Constraints;

use Ibexa\FieldTypePage\Validator\Constraints\NotBlankRichText;
use Ibexa\FieldTypePage\Validator\Constraints\NotBlankRichTextValidator;
use Ibexa\FieldTypeRichText\RichText\DOMDocumentFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class NotBlankRichTextValidatorTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    private NotBlankRichTextValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new NotBlankRichTextValidator(
            new DOMDocumentFactory()
        );
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider invalidValueDataProvider
     */
    public function testInvalidValue(?string $invalidValue): void
    {
        $constraint = new NotBlankRichText();
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraintViolationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->method('setCode')
            ->willReturn($constraintViolationBuilder);

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder
            ->expects($this->once())
            ->method('addViolation');

        $this->validator->validate($invalidValue, $constraint);
    }

    /**
     * @return array<string, array<int, string|null>>
     */
    public function invalidValueDataProvider(): array
    {
        return [
            'null_value' => [null],
            'empty_string' => [''],
            'empty_xml' => ['<xml></xml>'],
        ];
    }

    /**
     * @dataProvider validValueDataProvider
     */
    public function testValidValue(string $validValue): void
    {
        $constraint = new NotBlankRichText();

        $this->executionContext
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate($validValue, $constraint);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function validValueDataProvider(): array
    {
        return [
            'non_empty_xml' => ['<xml>Some data in xml</xml>'],
        ];
    }
}
