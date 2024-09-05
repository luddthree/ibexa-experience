<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Cache;

use Ibexa\Personalization\Cache\CachedModelService;
use Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcher;
use Ibexa\Personalization\Service\Model\ModelServiceInterface;
use Ibexa\Personalization\Value\Model\Attribute;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Stubs\Model\TestAttribute;

/**
 * @covers \Ibexa\Personalization\Cache\CachedModelService
 */
final class CachedModelServiceTest extends AbstractCacheTestCase
{
    /** @var \Ibexa\Personalization\Cache\CachedModelService */
    private $cachedModelService;

    /** @var \Ibexa\Personalization\Service\Model\ModelServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $innerModelService;

    /** @var int */
    private $customerId;

    public function setUp(): void
    {
        parent::setUp();

        $this->innerModelService = $this->createMock(ModelServiceInterface::class);
        $this->cachedModelService = new CachedModelService(
            $this->cache,
            $this->persistenceHandler,
            $this->persistenceLogger,
            $this->cacheIdentifierGenerator,
            $this->cacheIdentifierSanitizer,
            $this->locationPathConverter,
            $this->innerModelService
        );
        $this->customerId = 12345;
    }

    /**
     * @dataProvider providerForTestGetAttribute
     */
    public function testGetAttribute(
        Attribute $expectedAttribute,
        string $attributeKey,
        ?string $attributeSource = null,
        ?string $source = null
    ): void {
        $attributeType = TestAttribute::ATTRIBUTE_TYPE;
        $key = sprintf('ibexa-recommendation-attribute-12345-%s-%s', $attributeKey, $attributeType);

        $this->cache
            ->expects(self::once())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->getCacheItem($key));

        $this->innerModelService
            ->expects(self::once())
            ->method('getAttribute')
            ->with(
                $this->customerId,
                $attributeKey,
                $attributeType,
                $attributeSource,
                $source
            )
            ->willReturn($expectedAttribute);

        self::assertEquals(
            $expectedAttribute,
            $this->cachedModelService->getAttribute(
                $this->customerId,
                $attributeKey,
                $attributeType,
                $attributeSource,
                $source
            )
        );
    }

    /**
     * @return iterable<array{Attribute, string, 2?: string, 3?: string}>
     *
     * @throws \JsonException
     */
    public function providerForTestGetAttribute(): iterable
    {
        $paramAttribute = AttributeDataFetcher::PARAM_ATTRIBUTE;
        $body = Loader::load(Loader::MODEL_ATTRIBUTE_FIXTURE);
        $responseContents = json_decode($body, true, 512, JSON_THROW_ON_ERROR)[$paramAttribute];
        $attribute = Attribute::fromArray($responseContents);
        yield [
            $attribute,
            TestAttribute::ATTRIBUTE_KEY,
        ];
        yield [
            $attribute,
            TestAttribute::ATTRIBUTE_KEY,
            TestAttribute::ATTRIBUTE_SOURCE,
            TestAttribute::SOURCE,
        ];
    }
}
