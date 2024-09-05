<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteFactory\Form\DataTransformer;

use Ibexa\Bundle\SiteFactory\Form\Data\SiteMatcherConfigurationData;
use Ibexa\Bundle\SiteFactory\Form\DataTransformer\DomainNameTransformer;
use PHPUnit\Framework\TestCase;

class DomainNameTransformerTest extends TestCase
{
    /**
     * @dataProvider transformDataProvider
     */
    public function testTransform(?SiteMatcherConfigurationData $value, ?SiteMatcherConfigurationData $expected)
    {
        $transformer = new DomainNameTransformer();
        $result = $transformer->transform($value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider reverseTransformDataProvider
     */
    public function testReverseTransform(?SiteMatcherConfigurationData $value, ?SiteMatcherConfigurationData $expected)
    {
        $transformer = new DomainNameTransformer();
        $result = $transformer->reverseTransform($value);

        $this->assertEquals($expected, $result);
    }

    public function generateSiteMatcherConfigurationData(string $host): SiteMatcherConfigurationData
    {
        $siteMatcherConfigurationData = new SiteMatcherConfigurationData();
        $siteMatcherConfigurationData->setHost($host);

        return $siteMatcherConfigurationData;
    }

    public function transformDataProvider(): array
    {
        return [
            [$this->generateSiteMatcherConfigurationData('https://ibexa.co'), $this->generateSiteMatcherConfigurationData('https://ibexa.co')],
            [null, null],
        ];
    }

    public function reverseTransformDataProvider(): array
    {
        $siteMatcherConfigurationData = new SiteMatcherConfigurationData();
        $siteMatcherConfigurationData->setHost('ibexa.co');

        return [
            [$this->generateSiteMatcherConfigurationData('https://ibexa.co'), $siteMatcherConfigurationData],
            [$this->generateSiteMatcherConfigurationData('http://ibexa.co'), $siteMatcherConfigurationData],
            [null, null],
        ];
    }
}

class_alias(DomainNameTransformerTest::class, 'EzSystems\EzPlatformSiteFactoryBundle\Tests\Form\DataTransformer\DomainNameTransformerTest');
