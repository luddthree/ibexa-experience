<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\Type\Validation;

use Ibexa\Taxonomy\Form\Type\Validation\Constraint\Nested;
use Ibexa\Taxonomy\Form\Type\Validation\NestedValidator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class NestedValidatorTest extends TestCase
{
    /** @var \Symfony\Component\Validator\Context\ExecutionContextInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ExecutionContextInterface $executionContext;

    /** @var \Symfony\Component\Validator\Validator\ContextualValidatorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ContextualValidatorInterface $innerValidator;

    private NestedValidator $validator;

    protected function setUp(): void
    {
        $this->innerValidator = $this->createMock(ContextualValidatorInterface::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('inContext')->willReturn($this->innerValidator);

        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->executionContext->method('getValidator')->willReturn($validator);

        $this->validator = new NestedValidator();
        $this->validator->initialize($this->executionContext);
    }

    /**
     * @dataProvider dataProviderForTestSkipValidation
     *
     * @param mixed $value
     */
    public function testSkipValidation($value): void
    {
        $this->executionContext
            ->expects(self::never())
            ->method('buildViolation');

        $this->validator->validate($value, new Nested(
            static fn ($value) => $value,
            []
        ));
    }

    /**
     * @return iterable<string, array{object|null}>
     */
    public function dataProviderForTestSkipValidation(): iterable
    {
        yield 'null' => [null];
    }

    /**
     * @dataProvider dataProviderForTestValidate
     *
     * @param mixed $value
     * @param array<array{mixed, \Composer\Semver\Constraint\Constraint[]}> $expectedCalls
     */
    public function testValidate($value, Nested $constraint, array $expectedCalls): void
    {
        $actualCalls = [];

        // Validation should be delegated to dedicated constraints validators.
        $this->innerValidator
            ->method('validate')
            ->willReturnCallback(
                static function ($value, array $constraints) use (&$actualCalls): void {
                    $actualCalls[] = [$value, $constraints];
                }
            );

        $this->validator->validate($value, $constraint);

        $this->assertEquals($actualCalls, $expectedCalls);
    }

    /**
     * @return iterable<string, array{mixed, \Ibexa\Taxonomy\Form\Type\Validation\Constraint\Nested, array<array{mixed, array<\Symfony\Component\Validator\Constraint>}>}>
     */
    public function dataProviderForTestValidate(): iterable
    {
        $value = new stdClass();
        $value->foo = new stdClass();
        $value->foo->bar = 'baz';

        yield 'typical' => [
            $value,
            new Nested(
                static fn (stdClass $value): string => $value->foo->bar,
                [
                    new NotBlank(),
                    new Length(['max' => 10]),
                ]
            ),
            [
                ['baz', [new NotBlank(), new Length(['max' => 10])]],
            ],
        ];
    }
}
