<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory\Request;

use Ibexa\SiteFactory\Request\PathinfoExtractor;
use PHPUnit\Framework\TestCase;

final class PathinfoExtractorTest extends TestCase
{
    private const FOO_PATHINFO = '/';
    private const BAR_PATHINFO = '/bar';
    private const BAZ_PATHINFO = '/baz/bar';

    /**
     * @dataProvider dataProviderForGetFirstPathElement
     */
    public function testGetFirstPathElement(string $pathinfo, ?string $expected): void
    {
        $result = PathinfoExtractor::getFirstPathElement($pathinfo);

        $this->assertEquals(
            $expected,
            $result
        );
    }

    public function dataProviderForGetFirstPathElement(): iterable
    {
        return [
            'foo' => [
                self::FOO_PATHINFO,
                null,
            ],
            'bar' => [
                self::BAR_PATHINFO,
                'bar',
            ],
            'baz' => [
                self::BAZ_PATHINFO,
                'baz',
            ],
        ];
    }
}

class_alias(PathinfoExtractorTest::class, 'EzSystems\EzPlatformSiteFactory\Tests\Request\PathinfoExtractorTest');
