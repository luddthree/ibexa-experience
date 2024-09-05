<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\SelectionAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute as SelectionAttributeCriterion;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class SelectionAttributeTest extends TestCase
{
    private SelectionAttribute $parser;

    protected function setUp(): void
    {
        $this->parser = new SelectionAttribute();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new SelectionAttributeCriterion('foo', ['foo', 'bar']),
            $this->parser->parse(
                [
                    'SelectionAttributeCriterion' => [
                        'identifier' => 'foo',
                        'value' => ['foo', 'bar'],
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
            'Invalid <SelectionAttributeCriterion>',
            [
                'identifier' => 'test',
                'bar' => 'foo',
            ],
        ];
    }
}
