<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Connector\Dam\ParamConverter;

use Ibexa\Bundle\Connector\Dam\ParamConverter\AssetSourceParamConverter;
use Ibexa\Bundle\Connector\Dam\ParamConverter\QueryParamConverter;
use Ibexa\Connector\Dam\Search\QueryFactoryRegistry;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Search\Query;
use Ibexa\Contracts\Connector\Dam\Search\QueryFactory;
use Symfony\Component\HttpFoundation\Request;

final class QueryParamConverterTest extends AbstractParamConverterTest
{
    private const SUPPORTED_CLASS = Query::class;
    private const PARAMETER_NAME = 'query';

    /** @var \Ibexa\Bundle\Connector\Dam\ParamConverter\AssetSourceParamConverter */
    private $converter;

    /** @var \Ibexa\Connector\Dam\Search\QueryFactoryRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $queryFactoryRegistry;

    /** @var \Ibexa\Bundle\Connector\Dam\ParamConverter\AssetSourceParamConverter */
    private $assetSourceParamConverter;

    /** @var \Ibexa\Contracts\Connector\Dam\Search\QueryFactory|\PHPUnit\Framework\MockObject\MockObject */
    private $queryFactory;

    protected function setUp(): void
    {
        $this->queryFactory = $this->createMock(QueryFactory::class);

        $this->queryFactoryRegistry = $this->createMock(QueryFactoryRegistry::class);
        $this->queryFactoryRegistry
            ->method('getFactory')
            ->with(new AssetSource('test_source'))
            ->willReturn($this->queryFactory);

        $this->assetSourceParamConverter = new AssetSourceParamConverter();

        $this->converter = new QueryParamConverter(
            $this->queryFactoryRegistry,
            $this->assetSourceParamConverter
        );
    }

    public function testSupports(): void
    {
        $config = $this->createConfiguration(static::SUPPORTED_CLASS);

        $this->assertTrue($this->converter->supports($config));
    }

    /**
     * @dataProvider requestAttributesDataProvider
     */
    public function testApply(array $requestAttributes): void
    {
        $this->queryFactory
            ->method('buildFromRequest')
            ->willReturn(new Query('test_phrase'));

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function requestAttributesDataProvider(): array
    {
        return [
            'query_and_assetSource' => [[
                'query' => 'test_phrase',
                AssetSourceParamConverter::SOURCE_ASSET_NAME => new AssetSource('test_source'),
            ]],
            'query_and_source_name' => [[
                'query' => 'test_phrase',
                AssetSourceParamConverter::SOURCE_PARAM_NAME => 'test_source',
            ]],
        ];
    }
}

class_alias(QueryParamConverterTest::class, 'Ibexa\Platform\Tests\Bundle\Connector\Dam\ParamConverter\QueryParamConverterTest');
