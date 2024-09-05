<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\HtmlDiff;

use Ibexa\VersionComparison\HtmlDiff\Token\TokenFactory;
use Ibexa\VersionComparison\HtmlDiff\Token\TokenWrapper;
use PHPUnit\Framework\TestCase;

class TokenWrapperTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Token\TokenWrapper */
    private $wrapper;

    public function setUp(): void
    {
        $this->wrapper = new TokenWrapper(
            new TokenFactory()
        );
    }

    public function testWrap()
    {
        $tokenFactory = new TokenFactory();

        $result = $this->wrapper->wrap(
            [
                $tokenFactory->createToken('</b>'),
                $tokenFactory->createToken('this'),
                $tokenFactory->createToken(' '),
                $tokenFactory->createToken('is'),
                $tokenFactory->createToken(' '),
                $tokenFactory->createToken('a'),
                $tokenFactory->createToken(' '),
                $tokenFactory->createToken('<b>'),
                $tokenFactory->createToken('test'),
                $tokenFactory->createToken('</b>'),
                $tokenFactory->createToken('!'),
            ],
            'del',
            ''
        );

        $this->assertEquals(
            '</b><del>this is a </del><b><del>test</del></b><del>!</del>',
            $result
        );
    }
}

class_alias(TokenWrapperTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\HtmlDiff\TokenWrapperTest');
