<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\HtmlDiff;

use Ibexa\VersionComparison\HtmlDiff\Match\CommonBlock;
use Ibexa\VersionComparison\HtmlDiff\Match\FullMatchFinder;
use Ibexa\VersionComparison\HtmlDiff\Match\Segment;
use Ibexa\VersionComparison\HtmlDiff\Token\TokenFactory;
use PHPUnit\Framework\TestCase;

class FullMatchFinderTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Match\MatchFinder */
    private $matchFinder;

    public function setUp(): void
    {
        $this->matchFinder = new FullMatchFinder();
    }

    /**
     * @dataProvider findMatchDataProvider
     */
    public function testFindMatch(
        array $oldTokens,
        array $newTokens,
        array $expectedMatches
    ) {
        $result = $this->matchFinder->findMatchingBlocks(
            new Segment(
                $oldTokens,
                $newTokens,
                0,
                0
            )
        );

        $this->assertEquals(
            $expectedMatches,
            $result
        );
    }

    public function findMatchDataProvider()
    {
        $tokenFactory = new TokenFactory();

        return [
            [
                [
                    $tokenFactory->createToken('<h1>'),
                    $tokenFactory->createToken('Test'),
                    $tokenFactory->createToken('</h1>'),
                ],
                [
                    $tokenFactory->createToken('<h1>'),
                    $tokenFactory->createToken('Change'),
                    $tokenFactory->createToken('</h1>'),
                ],
                [
                    new CommonBlock(
                        0,
                        0,
                        1,
                        new Segment(
                            [
                                $tokenFactory->createToken('<h1>'),
                                $tokenFactory->createToken('Test'),
                                $tokenFactory->createToken('</h1>'),
                            ],
                            [
                                $tokenFactory->createToken('<h1>'),
                                $tokenFactory->createToken('Change'),
                                $tokenFactory->createToken('</h1>'),
                            ],
                            0,
                            0
                        )
                    ),
                    new CommonBlock(
                        1,
                        1,
                        1,
                        new Segment(
                            [
                                $tokenFactory->createToken('Test'),
                                $tokenFactory->createToken('</h1>'),
                            ],
                            [
                                $tokenFactory->createToken('Change'),
                                $tokenFactory->createToken('</h1>'),
                            ],
                            1,
                            1
                        )
                    ),
                ],
            ],
            [
                [
                    $tokenFactory->createToken('One'),
                    $tokenFactory->createToken('Two'),
                    $tokenFactory->createToken('Two'),
                ],
                [
                    $tokenFactory->createToken('One'),
                    $tokenFactory->createToken('Two'),
                    $tokenFactory->createToken('Three'),
                ],
                [
                    new CommonBlock(
                        0,
                        0,
                        2,
                        new Segment(
                            [
                                $tokenFactory->createToken('One'),
                                $tokenFactory->createToken('Two'),
                                $tokenFactory->createToken('Two'),
                            ],
                            [
                                $tokenFactory->createToken('One'),
                                $tokenFactory->createToken('Two'),
                                $tokenFactory->createToken('Three'),
                            ],
                            0,
                            0
                        )
                    ),
                ],
            ],
            [
                [
                    $tokenFactory->createToken('Old'),
                    $tokenFactory->createToken('Two'),
                    $tokenFactory->createToken('Two'),
                ],
                [
                    $tokenFactory->createToken('One'),
                    $tokenFactory->createToken('Two'),
                    $tokenFactory->createToken('Three'),
                ],
                [
                    new CommonBlock(
                        1,
                        1,
                        1,
                        new Segment(
                            [
                                $tokenFactory->createToken('Old'),
                                $tokenFactory->createToken('Two'),
                                $tokenFactory->createToken('Two'),
                            ],
                            [
                                $tokenFactory->createToken('One'),
                                $tokenFactory->createToken('Two'),
                                $tokenFactory->createToken('Three'),
                            ],
                            0,
                            0
                        )
                    ),
                ],
            ],
            [
                [
                    $tokenFactory->createToken('Old'),
                    $tokenFactory->createToken('Two'),
                    $tokenFactory->createToken('Three'),
                ],
                [
                    $tokenFactory->createToken('One'),
                    $tokenFactory->createToken('Three'),
                    $tokenFactory->createToken('Two'),
                ],
                [
                    new CommonBlock(
                        1,
                        2,
                        1,
                        new Segment(
                            [
                                $tokenFactory->createToken('Old'),
                                $tokenFactory->createToken('Two'),
                                $tokenFactory->createToken('Three'),
                            ],
                            [
                                $tokenFactory->createToken('One'),
                                $tokenFactory->createToken('Three'),
                                $tokenFactory->createToken('Two'),
                            ],
                            0,
                            0
                        )
                    ),
                ],
            ],
            [
                [
                    $tokenFactory->createToken('<span>'),
                    $tokenFactory->createToken('Old_Link_name'),
                    $tokenFactory->createToken('</span>'),
                ],
                [
                    $tokenFactory->createToken('<span>'),
                    $tokenFactory->createToken('</span>'),
                ],
                [
                    new CommonBlock(
                        0,
                        0,
                        1,
                        new Segment(
                            [
                                $tokenFactory->createToken('<span>'),
                                $tokenFactory->createToken('Old_Link_name'),
                                $tokenFactory->createToken('</span>'),
                            ],
                            [
                                $tokenFactory->createToken('<span>'),
                                $tokenFactory->createToken('</span>'),
                            ],
                            0,
                            0
                        )
                    ),
                    new CommonBlock(
                        1,
                        0,
                        1,
                        new Segment(
                            [
                                $tokenFactory->createToken('Old_Link_name'),
                                $tokenFactory->createToken('</span>'),
                            ],
                            [
                                $tokenFactory->createToken('</span>'),
                            ],
                            1,
                            1
                        )
                    ),
                ],
            ],
        ];
    }
}

class_alias(FullMatchFinderTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\HtmlDiff\FullMatchFinderTest');
