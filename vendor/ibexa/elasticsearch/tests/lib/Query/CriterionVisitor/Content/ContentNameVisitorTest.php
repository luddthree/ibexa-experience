<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\CriterionVisitor\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\CriterionVisitor\Content\ContentNameVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\Stub\TestCriterion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Elasticsearch\Query\CriterionVisitor\Content\ContentNameVisitor
 */
final class ContentNameVisitorTest extends TestCase
{
    private ContentNameVisitor $contentNameVisitor;

    private LanguageFilter $languageFilter;

    protected function setUp(): void
    {
        $this->contentNameVisitor = new ContentNameVisitor();
        $this->languageFilter = new LanguageFilter(
            [],
            true,
            true
        );
    }

    /**
     * @dataProvider provideDataForTestSupports
     */
    public function testSupports(
        bool $expected,
        Criterion $criterion
    ): void {
        self::assertSame(
            $expected,
            $this->contentNameVisitor->supports(
                $criterion,
                $this->languageFilter,
            )
        );
    }

    public function testVisit(): void
    {
        self::assertSame(
            [
                'wildcard' => [
                    'content_translated_name_s' => [
                        'value' => 'foo*',
                    ],
                ],
            ],
            $this->contentNameVisitor->visit(
                $this->createMock(CriterionVisitor::class),
                new Criterion\ContentName('foo*'),
                $this->languageFilter,
            )
        );
    }

    /**
     * @return iterable<array{
     *     bool,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion
     * }>
     */
    public function provideDataForTestSupports(): iterable
    {
        yield 'Unsupported criterion' => [
            false,
            new TestCriterion(
                null,
                null,
                'foo',
                null
            ),
        ];

        yield 'Supported criterion' => [
            true,
            new Criterion\ContentName('foo'),
        ];
    }
}
