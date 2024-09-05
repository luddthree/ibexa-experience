<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\REST\Input;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Taxonomy\REST\Input\Parser\TaxonomyEntryAssignToContent;
use Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryAssignToContent as TaxonomyEntryAssignToContentValue;

final class TaxonomyEntryAssignToContentTest extends AbstractInputParserTest
{
    /** @var \Ibexa\Taxonomy\REST\Input\Parser\TaxonomyEntryUnassignFromContent */
    protected Parser $parser;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    protected ContentService $contentService;

    protected function setUp(): void
    {
        $this->contentService = $this->createMock(ContentService::class);
        $this->contentService
            ->method('loadContent')
            ->willReturn(new Content());

        parent::setUp();

        $this->taxonomyService
            ->method('loadEntryById')
            ->withConsecutive([1], [2])
            ->willReturnOnConsecutiveCalls(
                $this->createTaxonomyEntry(1, 'Animal'),
                $this->createTaxonomyEntry(2, 'Car'),
            );
    }

    protected function getParserUnderTest(): Parser
    {
        return new TaxonomyEntryAssignToContent($this->contentService, $this->taxonomyService);
    }

    public function dataProviderForTestValidInput(): iterable
    {
        yield [
            [
                'content' => 123,
                'entries' => [1, 2],
            ],
            new TaxonomyEntryAssignToContentValue(
                new Content(),
                [
                    $this->createTaxonomyEntry(1, 'Animal'),
                    $this->createTaxonomyEntry(2, 'Car'),
                ],
            ),
        ];
    }

    public function dataProviderForTestInvalidInput(): iterable
    {
        yield 'empty array' => [
            [],
            "Missing 'Content' element for TaxonomyEntryAssignToContent.",
        ];

        yield 'missing content element' => [
            [
                'entries' => [1, 2],
            ],
            "Missing 'Content' element for TaxonomyEntryAssignToContent.",
        ];

        yield 'missing entries element' => [
            [
                'content' => 123,
            ],
            "Missing 'Entries' element for TaxonomyEntryAssignToContent.",
        ];
    }
}
