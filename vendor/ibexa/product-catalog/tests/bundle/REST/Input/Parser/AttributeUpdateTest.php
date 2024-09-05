<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\AttributeUpdate;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeUpdateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class AttributeUpdateTest extends TestCase
{
    private AttributeUpdate $parser;

    protected function setUp(): void
    {
        $this->parser = new AttributeUpdate();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new AttributeUpdateStruct(
                'color',
                [
                    'eng-US' => 'color',
                    'ger-DE' => 'farbe',
                ],
                [
                    'eng-US' => 'the way something is painted',
                    'ger-DE' => 'wie etwas gemalt ist',
                ],
                4,
                [
                    'option1' => 'option1',
                    'option2' => 'option2',
                ]
            ),
            $this->parser->parse(
                [
                    'identifier' => 'color',
                    'names' => [
                        'eng-US' => 'color',
                        'ger-DE' => 'farbe',
                    ],
                    'descriptions' => [
                        'eng-US' => 'the way something is painted',
                        'ger-DE' => 'wie etwas gemalt ist',
                    ],
                    'position' => 4,
                    'options' => [
                        'option1' => 'option1',
                        'option2' => 'option2',
                    ],
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );

        self::assertEquals(
            new AttributeUpdateStruct('width', [], [], 2),
            $this->parser->parse(
                [
                    'identifier' => 'width',
                    'position' => 2,
                ],
                $this->createMock(ParsingDispatcher::class)
            )
        );
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         string,
     *         array{
     *             descriptions?: array<string, string>,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Names corresponding to descriptions need to be provided.',
            [
                'descriptions' => [
                    'eng-GB' => 'color',
                    'ger-DE' => 'farbe',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         descriptions?: array<string, string>,
     *     },
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
}
