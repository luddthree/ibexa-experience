<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory\Values\Site;

use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use PHPUnit\Framework\TestCase;

final class SiteAccessMatcherConfigurationTest extends TestCase
{
    /**
     * @dataProvider dataProviderForGetUrl
     */
    public function testGetUrl(?string $host, ?string $path, string $expectedUrl): void
    {
        $configuration = new SiteAccessMatcherConfiguration($host, $path);
        $url = $configuration->getUrl();

        self::assertEquals($expectedUrl, $url);
    }

    public function dataProviderForGetUrl(): iterable
    {
        yield [null, null, ''];

        yield ['domain.com', null, 'domain.com'];

        yield ['domain.com', 'pl', 'domain.com/pl'];

        yield [null, 'pl', ''];
    }
}

class_alias(SiteAccessMatcherConfigurationTest::class, 'EzSystems\EzPlatformSiteFactory\Tests\Values\Site\SiteAccessMatcherConfigurationTest');
