<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\SiteFactory\ParamConverter;

use Ibexa\Bundle\SiteFactory\ParamConverter\SiteParamConverter;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\SiteFactory\Service\SiteService;
use Ibexa\Tests\Bundle\Core\Converter\AbstractParamConverterTest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteParamConverterTest extends AbstractParamConverterTest
{
    public const SUPPORTED_CLASS = Site::class;
    public const PARAMETER_NAME = 'site';

    /** @var \Ibexa\Bundle\SiteFactory\ParamConverter\SiteParamConverter */
    protected $converter;

    /** @var \Ibexa\SiteFactory\Service\SiteService|\PHPUnit\Framework\MockObject\MockObject */
    protected $serviceMock;

    protected function setUp(): void
    {
        $this->serviceMock = $this->createMock(SiteService::class);

        $this->converter = new SiteParamConverter($this->serviceMock);
    }

    public function testSupports()
    {
        $config = $this->createConfiguration(self::SUPPORTED_CLASS);

        $this->assertTrue($this->converter->supports($config));
    }

    /**
     * @dataProvider dataProvider
     *
     * @param mixed $siteId The site identifier fetched from the request
     * @param int $siteIdToLoad The site identifier used to load the site
     */
    public function testApply($siteId, int $siteIdToLoad)
    {
        $valueObject = new Site();

        $this->serviceMock
            ->expects($this->once())
            ->method('loadSite')
            ->with($siteIdToLoad)
            ->willReturn($valueObject);

        $requestAttributes = [
            SiteParamConverter::PARAMETER_SITE_ID => $siteId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertTrue($this->converter->apply($request, $config));
        $this->assertInstanceOf(self::SUPPORTED_CLASS, $request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWithWrongAttribute()
    {
        $requestAttributes = [
            SiteParamConverter::PARAMETER_SITE_ID => null,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get(self::PARAMETER_NAME));
    }

    public function testApplyWhenNotFound()
    {
        $siteId = 42;

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage(sprintf('Site %s not found.', $siteId));

        $this->serviceMock
            ->expects($this->once())
            ->method('loadSite')
            ->with($siteId)
            ->willThrowException($this->createMock(NotFoundException::class));

        $requestAttributes = [
            SiteParamConverter::PARAMETER_SITE_ID => $siteId,
        ];

        $request = new Request([], [], $requestAttributes);
        $config = $this->createConfiguration(self::SUPPORTED_CLASS, self::PARAMETER_NAME);

        $this->converter->apply($request, $config);
    }

    public function dataProvider(): array
    {
        return [
            'integer' => [42, 42],
            'number_as_string' => ['42', 42],
            'string' => ['42k', 42],
        ];
    }
}

class_alias(SiteParamConverterTest::class, 'EzSystems\EzPlatformSiteFactoryBundle\Tests\ParamConverter\SiteParamConverterTest');
