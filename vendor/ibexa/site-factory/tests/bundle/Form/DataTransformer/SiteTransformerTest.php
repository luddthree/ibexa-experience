<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteFactory\Form\DataTransformer;

use Ibexa\Bundle\SiteFactory\Form\DataTransformer\SiteTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\SiteFactory\Service\SiteService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SiteTransformerTest extends TestCase
{
    private const SITE_ID = 123456;
    private const STRING_AS_SITE_ID = 'string';

    /**
     * @dataProvider transformDataProvider
     *
     * @param $value
     * @param $expected
     */
    public function testTransform($value, $expected)
    {
        $service = $this->createMock(SiteService::class);
        $transformer = new SiteTransformer($service);

        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider transformWithInvalidInputDataProvider
     *
     * @param $value
     */
    public function testTransformWithInvalidInput($value)
    {
        $languageService = $this->createMock(SiteService::class);
        $transformer = new SiteTransformer($languageService);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . Site::class . ' object.');

        $transformer->transform($value);
    }

    public function testReverseTransformWithId()
    {
        $service = $this->createMock(SiteService::class);
        $service->expects(self::once())
            ->method('loadSite')
            ->with(self::SITE_ID)
            ->willReturn(new Site(['id' => self::SITE_ID]));

        $transformer = new SiteTransformer($service);

        $result = $transformer->reverseTransform(self::SITE_ID);

        $this->assertEquals(new Site(['id' => self::SITE_ID]), $result);
    }

    public function testReverseTransformWithNull()
    {
        $service = $this->createMock(SiteService::class);
        $service->expects(self::never())
            ->method('loadSite');

        $transformer = new SiteTransformer($service);

        $result = $transformer->reverseTransform(null);

        $this->assertNull($result);
    }

    public function testReverseTransformWithNotFoundException()
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Site not found');

        $service = $this->createMock(SiteService::class);
        $service->method('loadSite')
            ->will($this->throwException(new class('Site not found') extends NotFoundException {
            }));

        $transformer = new SiteTransformer($service);

        $transformer->reverseTransform(654321);
    }

    public function testReverseTransformWithNonNumericString(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a numeric string.');

        $service = $this->createMock(SiteService::class);
        $service->expects(self::never())->method('loadSite');

        $transformer = new SiteTransformer($service);
        $transformer->reverseTransform(self::STRING_AS_SITE_ID);
    }

    /**
     * @return array
     */
    public function transformDataProvider(): array
    {
        $transform = new Site(['id' => self::SITE_ID]);

        return [
            'with_id' => [$transform, self::SITE_ID],
            'null' => [null, null],
        ];
    }

    /**
     * @return array
     */
    public function transformWithInvalidInputDataProvider(): array
    {
        return [
            'string' => [self::STRING_AS_SITE_ID],
            'integer' => [self::SITE_ID],
            'bool' => [true],
            'float' => [12.34],
            'array' => [[]],
            'object' => [new \stdClass()],
        ];
    }
}

class_alias(SiteTransformerTest::class, 'EzSystems\EzPlatformSiteFactoryBundle\Tests\Form\DataTransformer\SiteTransformerTest');
