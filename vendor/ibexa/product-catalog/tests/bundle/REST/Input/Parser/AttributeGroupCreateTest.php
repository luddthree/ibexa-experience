<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\AttributeGroupCreate;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupCreateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class AttributeGroupCreateTest extends TestCase
{
    private AttributeGroupCreate $parser;

    protected function setUp(): void
    {
        $this->parser = new AttributeGroupCreate();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new AttributeGroupCreateStruct(
                [
                    'eng-GB' => 'name-eng',
                    'pol-PL' => 'name-pl',
                ],
                'test-identifier',
                0,
                []
            ),
            $this->parser->parse(
                [
                    'names' => [
                        'eng-GB' => 'name-eng',
                        'pol-PL' => 'name-pl',
                    ],
                    'identifier' => 'test-identifier',
                    'position' => 0,
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
     *             names?: mixed,
     *             identifier?: string,
     *             position?: integer,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (identifier, names, position) for Attribute Group.',
            [],
        ];

        yield [
            'Missing properties (identifier) for Attribute Group.',
            [
                'names' => ['eng-GB'],
                'position' => 3,
            ],
        ];

        yield [
            'Missing properties (names) for Attribute Group.',
            [
                'identifier' => 'test-identifier',
                'position' => 2,
            ],
        ];

        yield [
            'Missing properties (position) for Attribute Group.',
            [
                'identifier' => 'test-identifier',
                'names' => ['eng-GB'],
            ],
        ];

        yield [
            'The "names" parameter must be an array.',
            [
                'identifier' => 'test-identifier',
                'names' => 'eng-GB',
                'position' => 2,
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         names: string[],
     *         identifier?: string,
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
