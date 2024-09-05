<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use DateTime;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CreatedAt;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAt as CreatedAtCriterion;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class CreatedAtTest extends TestCase
{
    private CreatedAt $parser;

    protected function setUp(): void
    {
        $this->parser = new CreatedAt();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new CreatedAtCriterion(new DateTime('31-12-2000'), '>='),
            $this->parser->parse(
                [
                    'CreatedAtCriterion' => [
                        'created_at' => '31-12-2000',
                        'operator' => '>=',
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
            'Invalid <CreatedAtCriterion>',
            [
                'identifier' => 'test',
                'bar' => 'foo',
            ],
        ];
    }
}
