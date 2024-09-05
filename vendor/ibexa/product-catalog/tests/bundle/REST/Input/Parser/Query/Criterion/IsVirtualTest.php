<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\IsVirtual;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual as IsVirtualCriterion;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\ParserTools;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\IsVirtual
 */
final class IsVirtualTest extends TestCase
{
    private const IS_VIRTUAL_CRITERION = 'IsVirtualCriterion';

    private IsVirtual $parser;

    /** @var \Symfony\Component\Validator\Validator\ValidatorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->parser = new IsVirtual(
            $this->validator,
            new ParserTools()
        );
    }

    public function testValidInput(): void
    {
        $data = [self::IS_VIRTUAL_CRITERION => true];
        $this->mockValidatorValidate(
            $data,
            new ConstraintViolationList()
        );

        self::assertEquals(
            new IsVirtualCriterion(true),
            $this->parser->parse(
                $data,
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array<string, string>
     * } $input
     */
    public function testInvalidInput(string $exceptionMessage, array $input): void
    {
        $violationList[] = $this->createMock(ConstraintViolationInterface::class);

        $this->mockValidatorValidate(
            $input,
            new ConstraintViolationList($violationList)
        );

        $this->expectException(Parser::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->parser->parse(
            $input,
            $this->createMock(ParsingDispatcher::class)
        );
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         string,
     *         array<string, string>,
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Invalid <IsVirtualCriterion>',
            [
                'bar' => 'foo',
            ],
        ];
    }

    /**
     * @param array<mixed> $data
     */
    private function mockValidatorValidate(
        array $data,
        ConstraintViolationListInterface $violationList
    ): void {
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with(
                $data,
                new Assert\Collection(
                    ['fields' => [self::IS_VIRTUAL_CRITERION => new Assert\AtLeastOneOf([
                        'constraints' => [
                            new Assert\Type('boolean'),
                            new Assert\Choice(
                                [
                                    'true', 'false',
                                ]
                            ),
                        ],
                    ])]]
                ),
            )
            ->willReturn($violationList);
    }
}
