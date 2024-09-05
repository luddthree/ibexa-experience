<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\REST\Input;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Rest\Exceptions\Parser as ParserException;
use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\Values\Content\Content;
use PHPUnit\Framework\TestCase;

abstract class AbstractInputParserTest extends TestCase
{
    protected Parser $parser;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected TaxonomyServiceInterface $taxonomyService;

    /** @var \Ibexa\Contracts\Rest\Input\ParsingDispatcher|\PHPUnit\Framework\MockObject\MockObject */
    protected ParsingDispatcher $parsingDispatcher;

    protected function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->parsingDispatcher = $this->createMock(ParsingDispatcher::class);
        $this->parser = $this->getParserUnderTest();
    }

    /**
     * @dataProvider dataProviderForTestValidInput
     *
     * @param array<string, mixed> $input
     */
    public function testValidInput(array $input, ValueObject $expectedResult): void
    {
        $result = $this->parser->parse(
            $input,
            $this->parsingDispatcher,
        );

        self::assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider dataProviderForTestInvalidInput
     *
     * @param array<string, mixed> $input
     */
    public function testInvalidInput(array $input, string $expectedMessage): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->parser->parse(
            $input,
            $this->parsingDispatcher,
        );
    }

    protected function createTaxonomyEntry(
        int $id,
        string $identifier,
        ?string $taxonomy = null
    ): TaxonomyEntry {
        $name = ucfirst($identifier);

        return new TaxonomyEntry(
            $id,
            $identifier,
            $name,
            'eng-GB',
            [
                'eng-GB' => $name,
            ],
            null,
            new Content(),
            $taxonomy ?? 'tags'
        );
    }

    abstract protected function getParserUnderTest(): Parser;

    /**
     * @return iterable<array{
     *      array<string, mixed>,
     *      \Ibexa\Contracts\Core\Repository\Values\ValueObject
     * }>
     */
    abstract public function dataProviderForTestValidInput(): iterable;

    /**
     * @return iterable<array{
     *      array<string, mixed>,
     *      string
     * }>
     */
    abstract public function dataProviderForTestInvalidInput(): iterable;
}
