<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use DateTime;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CreatedAtRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAtRange as CreatedAtRangeCriterion;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class CreatedAtRangeTest extends TestCase
{
    private CreatedAtRange $parser;

    protected function setUp(): void
    {
        $this->parser = new CreatedAtRange();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new CreatedAtRangeCriterion(
                new DateTime('31-12-2000'),
                new DateTime('31-12-2020')
            ),
            $this->parser->parse(
                [
                    'CreatedAtRangeCriterion' => [
                        'min' => '31-12-2000',
                        'max' => '31-12-2020',
                    ],
                ],
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
            'Invalid <CreatedAtRangeCriterion>',
            [
                'identifier' => 'test',
                'bar' => 'foo',
            ],
        ];
    }
}
