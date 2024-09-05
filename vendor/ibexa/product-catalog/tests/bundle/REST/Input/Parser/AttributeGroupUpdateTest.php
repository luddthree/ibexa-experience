<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\AttributeGroupUpdate;
use Ibexa\Bundle\ProductCatalog\REST\Value\AttributeGroupUpdateStruct;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;

final class AttributeGroupUpdateTest extends TestCase
{
    private AttributeGroupUpdate $parser;

    protected function setUp(): void
    {
        $this->parser = new AttributeGroupUpdate();
    }

    public function testValidInput(): void
    {
        self::assertEquals(
            new AttributeGroupUpdateStruct(
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

        self::assertEquals(
            new AttributeGroupUpdateStruct([], 'test2-identifier', 3, []),
            $this->parser->parse(
                [
                    'identifier' => 'test2-identifier',
                    'position' => 3,
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
     *             names: string[],
     *             identifier?: string,
     *             position?: integer,
     *             languages?: string,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'The "languages" parameter must be an array.',
            [
                'identifier' => 'test-identifier',
                'names' => ['eng-GB'],
                'position' => 2,
                'languages' => 'pol-PL',
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
     *         languages?: string,
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
