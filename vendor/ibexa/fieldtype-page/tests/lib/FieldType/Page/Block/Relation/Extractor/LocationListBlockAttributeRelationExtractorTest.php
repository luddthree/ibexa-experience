<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\FieldType\Page\Block\Relation\Extractor;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper;
use Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor\LocationListBlockAttributeRelationExtractor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\FieldTypePage\FieldType\Page\Block\Relation\Extractor\LocationListBlockAttributeRelationExtractor
 */
final class LocationListBlockAttributeRelationExtractorTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\Core\Repository\LocationService */
    private LocationService $locationService;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper */
    private ContentLocationMapper $contentLocationMapper;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page */
    private Page $page;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue */
    private BlockValue $blockValue;

    /** @var \PHPUnit\Framework\MockObject\MockObject&\Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition */
    private BlockAttributeDefinition $attributeDefinition;

    protected function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);
        $this->contentLocationMapper = $this->createMock(ContentLocationMapper::class);
        $this->page = $this->createMock(Page::class);
        $this->blockValue = $this->createMock(BlockValue::class);
        $this->attributeDefinition = $this->createMock(BlockAttributeDefinition::class);
    }

    /**
     * @phpstan-return iterable<array{
     *     string|int,
     *     array<integer>
     * }>
     */
    public function providerForTestExtract(): iterable
    {
        yield  [
            12, // single int attribute
            [12],
        ];
        yield [
            '12,45,70', // comma separated int values
            [12, 45, 70],
        ];
        yield [
            '', // empty string
            [],
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *     array<integer>
     * }>
     */
    public function providerForTestExtractWhenAttributeIsInvalid(): iterable
    {
        yield [[12, 45]]; // array of integers
    }

    /**
     * @dataProvider providerForTestExtract
     *
     * @param string|int $attributeValue
     * @param array<integer> $expectedResult
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testExtract($attributeValue, array $expectedResult): void
    {
        $this->contentLocationMapper
            ->method('hasMapping')
            ->with(self::isType('int'))
            ->willReturnCallback(static function ($value): bool {
                return (int)$value > 0;
            });

        $this->contentLocationMapper
            ->method('getMapping')
            ->with(self::isType('int'))
            ->willReturnCallback(static function ($value): int {
                return (int)$value;
            });

        $extractor = new LocationListBlockAttributeRelationExtractor($this->locationService, $this->contentLocationMapper);

        $attribute = new Attribute('1', 'foobar', $attributeValue);
        $result = $extractor->extract(
            $this->page,
            $this->blockValue,
            $this->attributeDefinition,
            $attribute
        );

        self::assertEquals($expectedResult, $result, 'Result does not match expected result.');
    }

    /**
     * @dataProvider providerForTestExtractWhenAttributeIsInvalid
     *
     * @param array<integer> $attributeValue
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testExtractWhenAttributeIsInvalid(array $attributeValue): void
    {
        $extractor = new LocationListBlockAttributeRelationExtractor($this->locationService, $this->contentLocationMapper);
        $attribute = new Attribute('1', 'foobar', $attributeValue);

        $this->expectException(InvalidArgumentException::class);
        $extractor->extract(
            $this->page,
            $this->blockValue,
            $this->attributeDefinition,
            $attribute
        );
    }
}
