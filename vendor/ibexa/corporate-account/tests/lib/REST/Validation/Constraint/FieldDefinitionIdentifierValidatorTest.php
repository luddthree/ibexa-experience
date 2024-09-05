<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\CorporateAccount\REST\Validation\Constraint;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\CorporateAccount\REST\Validation\Constraint\FieldDefinitionIdentifier;
use Ibexa\CorporateAccount\REST\Validation\Constraint\FieldDefinitionIdentifierValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @covers \Ibexa\CorporateAccount\REST\Validation\Constraint\FieldDefinitionIdentifierValidator
 */
final class FieldDefinitionIdentifierValidatorTest extends ConstraintValidatorTestCase
{
    public function testValidate(): void
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->method('getFieldDefinition')
            ->with('name')
            ->willReturn($this->createMock(FieldDefinition::class));

        $constraint = new FieldDefinitionIdentifier($contentType);
        $this->validator->validate('name', $constraint);

        $this->assertNoViolation();
    }

    public function testValidateInvalidIdentifier(): void
    {
        $contentType = $this->createMock(ContentType::class);
        $contentType
            ->method('getFieldDefinition')
            ->withAnyParameters()
            ->willReturn(null);

        $contentType
            ->method('__get')
            ->with('identifier')
            ->willReturn('bar');

        $constraint = new FieldDefinitionIdentifier($contentType);

        $this->validator->validate('foo', $constraint);

        $this->buildViolation(
            '{{ value }} is an invalid Field Definition identifier for {{ content_type }} ' .
            'content type in Field Value structure'
        )
            ->setParameter('{{ value }}', sprintf('"%s"', 'foo'))
            ->setParameter('{{ content_type }}', sprintf('"%s"', 'bar'))
            ->setCode(FieldDefinitionIdentifier::IS_INVALID_FIELD_DEFINITION_IDENTIFIER_ERROR)
            ->assertRaised();
    }

    protected function createValidator(): FieldDefinitionIdentifierValidator
    {
        return new FieldDefinitionIdentifierValidator();
    }
}
