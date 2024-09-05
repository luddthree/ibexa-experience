<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\HtmlDiff;

use Ibexa\VersionComparison\HtmlDiff\DiffBuilder;
use Ibexa\VersionComparison\HtmlDiff\Lexer\Tokenizer;
use Ibexa\VersionComparison\HtmlDiff\Match\FullMatchFinder;
use Ibexa\VersionComparison\HtmlDiff\Operation\DeleteOperation;
use Ibexa\VersionComparison\HtmlDiff\Operation\EqualOperation;
use Ibexa\VersionComparison\HtmlDiff\Operation\InsertOperation;
use Ibexa\VersionComparison\HtmlDiff\Operation\ReplaceOperation;
use Ibexa\VersionComparison\HtmlDiff\Token\TokenFactory;
use Ibexa\VersionComparison\HtmlDiff\Token\TokenWrapper;
use PHPUnit\Framework\TestCase;

class DiffBuilderTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\DiffBuilder */
    private $diffBuilder;

    public function setUp(): void
    {
        $this->diffBuilder = new DiffBuilder(
            new FullMatchFinder(),
            new Tokenizer(
                new TokenFactory()
            ),
            new TokenWrapper(
                new TokenFactory()
            )
        );
    }

    /**
     * @dataProvider operationsDataProvider
     *
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $oldTokens
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $newTokens
     * @param \Ibexa\VersionComparison\HtmlDiff\Operation\Operation[] $expectedOperations
     */
    public function testGetOperations(
        array $oldTokens,
        array $newTokens,
        array $expectedOperations
    ) {
        $result = $this->diffBuilder->getOperations($oldTokens, $newTokens);

        $this->assertEquals(
            $expectedOperations,
            $result
        );
    }

    public function operationsDataProvider()
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
                    new EqualOperation(0, 0, 0, 0),
                    new ReplaceOperation(
                        new DeleteOperation(1, 1, 1, 1),
                        new InsertOperation(1, 1, 1, 1)
                    ),
                    new EqualOperation(2, 2, 2, 2),
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
                    new ReplaceOperation(
                        new DeleteOperation(0, 0, 0, 1),
                        new InsertOperation(0, 0, 0, 1)
                    ),
                    new EqualOperation(1, 1, 2, 2),
                    new DeleteOperation(2, 2, 3, 2),
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
                    new EqualOperation(0, 0, 0, 0),
                    new DeleteOperation(1, 1, 1, 0),
                    new EqualOperation(2, 2, 1, 1),
                ],
            ],
        ];
    }

    /**
     * @dataProvider htmlDataProvider
     */
    public function testBuild(
        string $oldHtml,
        string $newHtml,
        string $expectedDiff
    ) {
        $result = $this->diffBuilder->build($oldHtml, $newHtml);

        $this->assertEquals(
            $expectedDiff,
            $result
        );
    }

    public function htmlDataProvider()
    {
        return [
            [
                '<h1>Test</h1>',
                '<h1>Change</h1>',
                '<h1><del class="diffmod">Test</del><ins class="diffmod">Change</ins></h1>',
            ],
            [
                '<span><a href="localhost">Old_Link_name</a></span>',
                '<span><a href="localhost"></a></span>',
                '<span><a href="localhost"><del class="diffdel">Old_Link_name</del></a></span>',
            ],
            [
                '<span><a href="localhost"></a></span>',
                '<span><a href="localhost">Link name added</a></span>',
                '<span><a href="localhost"><ins class="diffins">Link name added</ins></a></span>',
            ],
            [
                '<table border="1" style="width:100%;">
                <tbody>
                <tr><td>One</td><td>4</td><td>5</td></tr><tr><td>2</td><td>Two</td><td>00</td></tr><tr><td>1</td><td>5</td><td>Three</td></tr>
                </tbody>
                </table>',
                '<table border="1" style="width:100%;">
                <tbody>
                <tr><td>One</td><td>n</td></tr><tr><td>2</td><td>n1</td></tr><tr><td>1</td><td>n2</td></tr>
                </tbody>
                </table>',
                '<table border="1" style="width:100%;">
                <tbody>
                <tr><td>One</td><td><del class="diffmod">4</del></td><td><del class="diffmod">5</del><ins class="diffmod">n</ins></td></tr><tr><td>2</td><td><del class="diffmod">Two</del></td><td><del class="diffmod">00</del><ins class="diffmod">n1</ins></td></tr><tr><td>1</td><td><del class="diffmod">5</del></td><td><del class="diffmod">Three</del><ins class="diffmod">n2</ins></td></tr>
                </tbody>
                </table>',
            ],
            [
                '<figure class="ezimage-field">
                <img src="test_old.png" alt="removed" height="200" width="200" />
                </figure>',
                '<figure class="ezimage-field">
                <img src="change.jpg" alt="added" height="200" width="200" />
                </figure>',
                '<figure class="ezimage-field">
                <del class="diffmod"><img src="test_old.png" alt="removed" height="200" width="200" /></del><ins class="diffmod"><img src="change.jpg" alt="added" height="200" width="200" /></ins>
                </figure>',
            ],
            [
                '<iframe type="text/html" src="//www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0" frameborder="0"></iframe>',
                '<iframe type="text/html" src="//www.youtube.com/embed/t8OgIMbIhH8?autoplay=0" frameborder="0"></iframe>',
                '<del class="diffmod"><iframe type="text/html" src="//www.youtube.com/embed/dQw4w9WgXcQ?autoplay=0" frameborder="0"></iframe></del><ins class="diffmod"><iframe type="text/html" src="//www.youtube.com/embed/t8OgIMbIhH8?autoplay=0" frameborder="0"></iframe></ins>',
            ],
        ];
    }
}

class_alias(DiffBuilderTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\HtmlDiff\DiffBuilderTest');
