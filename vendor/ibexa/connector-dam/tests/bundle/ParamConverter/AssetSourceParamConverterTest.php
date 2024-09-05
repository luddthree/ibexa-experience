<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Connector\Dam\ParamConverter;

use Ibexa\Bundle\Connector\Dam\ParamConverter\AssetSourceParamConverter;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class AssetSourceParamConverterTest extends AbstractParamConverterTest
{
    public const SUPPORTED_CLASS = AssetSource::class;
    public const PARAMETER_NAME = 'assetSource';

    /** @var \Ibexa\Bundle\Connector\Dam\ParamConverter\AssetSourceParamConverter */
    private $converter;

    protected function setUp(): void
    {
        $this->converter = new AssetSourceParamConverter();
    }

    public function testSupports(): void
    {
        $config = $this->createConfiguration(static::SUPPORTED_CLASS);

        $this->assertTrue($this->converter->supports($config));
    }

    public function testApply(): void
    {
        $requestAttributes = [
            AssetSourceParamConverter::SOURCE_PARAM_NAME => 'test_source',
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithNoProvidedSource(): void
    {
        $requestAttributes = [];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Request do not contain all required arguments');
        $this->converter->apply($request, $config);
    }
}

class_alias(AssetSourceParamConverterTest::class, 'Ibexa\Platform\Tests\Bundle\Connector\Dam\ParamConverter\AssetSourceParamConverterTest');
