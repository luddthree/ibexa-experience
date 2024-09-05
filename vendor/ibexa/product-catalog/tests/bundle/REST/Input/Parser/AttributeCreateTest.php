<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\AttributeCreate;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeCreateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class AttributeCreateTest extends TestCase
{
    private AttributeCreate $parser;

    protected function setUp(): void
    {
        $this->parser = new AttributeCreate();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new AttributeCreateStruct(
                'color',
                'selection',
                'appearance',
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
                    'type' => 'selection',
                    'group' => 'appearance',
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
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         string,
     *         array{
     *             identifier?: string,
     *             type?: string,
     *             group?: string,
     *             names?: mixed,
     *             descriptions?: mixed,
     *             position?: integer,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (identifier, type, group) for Attribute.',
            [
                'names' => [
                    'eng-GB' => 'color',
                ],
                'position' => 3,
            ],
        ];

        yield [
            'Missing properties (names, position) for Attribute.',
            [
                'group' => 'colors',
                'identifier' => 'color',
                'type' => 'checkbox',
            ],
        ];

        yield [
            'Missing properties (type, group, names) for Attribute.',
            [
                'identifier' => 'test-identifier',
                'position' => 2,
            ],
        ];

        yield [
            'The "descriptions" parameter must be an array.',
            [
                'identifier' => 'test-identifier',
                'descriptions' => 'description',
                'position' => 2,
                'names' => [
                    'eng-GB' => 'color',
                ],
                'group' => 'colors',
                'type' => 'selection',
                'options' => [
                    'foo' => 'bar',
                ],
            ],
        ];

        yield [
            'The "names" parameter must be an array.',
            [
                'identifier' => 'test-identifier',
                'names' => 'eng-GB',
                'position' => 2,
                'group' => 'colors',
                'type' => 'selection',
                'options' => [
                    'foo' => 'bar',
                ],
            ],
        ];

        yield [
            'The "options" parameter must be an array.',
            [
                'identifier' => 'test-identifier',
                'names' => ['eng-GB'],
                'position' => 2,
                'group' => 'colors',
                'type' => 'selection',
                'options' => 'option1',
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         identifier?: string,
     *         type?: string,
     *         group?: string,
     *         names?: array<string, string>,
     *         descriptions?: array<string, string>,
     *         position?: integer,
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
