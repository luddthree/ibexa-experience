<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\REST\Input;

use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\Taxonomy\REST\Input\Parser\TaxonomyEntryBulkRemove;

final class TaxonomyEntryBulkRemoveTest extends AbstractInputParserTest
{
    /** @var \Ibexa\Taxonomy\REST\Input\Parser\TaxonomyEntryBulkRemove */
    protected Parser $parser;

    protected function setUp(): void
    {
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
        return new TaxonomyEntryBulkRemove($this->taxonomyService);
    }

    public function dataProviderForTestValidInput(): iterable
    {
        yield [
            ['entries' => [1, 2]],
            new \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryBulkRemove([
                $this->createTaxonomyEntry(1, 'Animal'),
                $this->createTaxonomyEntry(2, 'Car'),
            ]),
        ];
    }

    public function dataProviderForTestInvalidInput(): iterable
    {
        yield 'empty array' => [
            [],
            "Missing 'Entries' element for TaxonomyEntryBulkRemove",
        ];
    }
}
