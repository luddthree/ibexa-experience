<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Model;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcher;
use Ibexa\Personalization\Exception\UnsupportedModelAttributeTypeException;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Stubs\Model\TestAttribute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcher
 */
final class AttributeDataFetcherTest extends AbstractConsumerTestCase
{
    /** @var \Ibexa\Personalization\Client\Consumer\Model\AttributeDataFetcher */
    private $attributeDataFetcher;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->attributeDataFetcher = new AttributeDataFetcher(
            $this->client,
            'https://endpoint.com'
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
    }

    public function testThrowExceptionWhenGivenAttributeTypeIsUnsupported(): void
    {
        $unsupportedType = 'bar';
        $allowedAttibuteTypes = ['NOMINAL', 'NUMERIC'];

        $this->expectException(UnsupportedModelAttributeTypeException::class);
        $this->expectExceptionMessage(
            'Model attibute type: bar is unsupported. Allowed types: ' . implode(', ', $allowedAttibuteTypes)
        );

        $this->attributeDataFetcher->fetchAttribute(
            $this->customerId,
            $this->licenseKey,
            TestAttribute::ATTRIBUTE_KEY,
            /** @phpstan-ignore-next-line */
            $unsupportedType,
        );
    }

    /**
     * @dataProvider providerForTestFetchAttribute
     */
    public function testFetchAttribute(
        ResponseInterface $expectedResponse,
        UriInterface $uri,
        string $attributeKey,
        ?string $attributeSource = null,
        ?string $source = null
    ): void {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                $uri,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn($expectedResponse);

        self::assertEquals(
            $expectedResponse,
            $this->attributeDataFetcher->fetchAttribute(
                $this->customerId,
                $this->licenseKey,
                $attributeKey,
                TestAttribute::ATTRIBUTE_TYPE,
                $attributeSource,
                $source
            )
        );
    }

    /**
     * @return iterable<array{ResponseInterface, UriInterface, string, 3?: string, 4?: string}>
     */
    public function providerForTestFetchAttribute(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::MODEL_ATTRIBUTE_FIXTURE)
            ),
            new Uri('https://endpoint.com/api/v3/12345/structure/get_attribute_values/NOMINAL/foo'),
            TestAttribute::ATTRIBUTE_KEY,
        ];
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::MODEL_ATTRIBUTE_FIXTURE)
            ),
            new Uri('https://endpoint.com/api/v3/12345/structure/get_attribute_values/NOMINAL/foo/USER/allSources'),
            TestAttribute::ATTRIBUTE_KEY,
            TestAttribute::ATTRIBUTE_SOURCE,
            TestAttribute::SOURCE,
        ];
    }
}
