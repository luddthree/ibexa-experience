<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\REST\Validation\Constraint;

use Ibexa\CorporateAccount\REST\Validation\Constraint\MediaType;
use Ibexa\CorporateAccount\REST\Validation\Constraint\MediaTypeValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @covers \Ibexa\CorporateAccount\REST\Validation\Constraint\MediaTypeValidator
 */
final class MediaTypeValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @return iterable<string, array{\Ibexa\CorporateAccount\REST\Validation\Constraint\MediaType, string}>
     */
    public function getDataForTestInvalidMediaType(): iterable
    {
        yield 'Resource Name mismatch' => [
            new MediaType('CompanyList'),
            'application/vnd.ibexa.api.Role+xml',
        ];

        yield 'Invalid Media-Type prefix' => [
            new MediaType('Role'),
            'foo',
        ];
    }

    /**
     * @return iterable<string, array{\Ibexa\CorporateAccount\REST\Validation\Constraint\MediaType, string}>
     */
    public function getDataForTestValidMediaType(): iterable
    {
        $roleMediaTypeConstraint = new MediaType('Role');
        yield 'Simple Resource XML' => [
            $roleMediaTypeConstraint,
            'application/vnd.ibexa.api.Role+xml',
        ];

        yield 'Simple Resource JSON' => [
            $roleMediaTypeConstraint,
            'application/vnd.ibexa.api.Role+json',
        ];
    }

    /**
     * @dataProvider getDataForTestInvalidMediaType
     */
    public function testInvalidMediaType(MediaType $constraint, string $inputMediaType): void
    {
        $this->validator->validate($inputMediaType, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', sprintf('"%s"', $inputMediaType))
            ->setParameter(
                '{{ expectedResourceName }}',
                $constraint->expectedResourceName
            )
            ->setCode(MediaType::IS_MALFORMED_MEDIA_TYPE_ERROR)
            ->assertRaised();
    }

    /**
     * @dataProvider getDataForTestValidMediaType
     */
    public function testValidMediaType(MediaType $constraint, string $inputMediaType): void
    {
        $this->validator->validate($inputMediaType, $constraint);

        $this->assertNoViolation();
    }

    protected function createValidator(): MediaTypeValidator
    {
        return new MediaTypeValidator();
    }
}
