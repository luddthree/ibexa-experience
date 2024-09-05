<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\REST\Input;

use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\REST\Input\Parser\TaxonomyEntryMove;
use Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryMove as TaxonomyEntryMoveValue;

final class TaxonomyEntryMoveTest extends AbstractInputParserTest
{
    /** @var \Ibexa\Taxonomy\REST\Input\Parser\TaxonomyEntryMove */
    protected Parser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taxonomyService
            ->method('loadEntryById')
            ->withConsecutive([1], [2])
            ->willReturnOnConsecutiveCalls(
                $this->createTaxonomyEntry(1, 'foo'),
                $this->createTaxonomyEntry(2, 'bar'),
            );
    }

    protected function getParserUnderTest(): Parser
    {
        return new TaxonomyEntryMove($this->taxonomyService);
    }

    public function dataProviderForTestValidInput(): iterable
    {
        yield [
            [
                'entry' => 1,
                'sibling' => 2,
                'position' => 'next',
            ],
            new TaxonomyEntryMoveValue(
                $this->createTaxonomyEntry(1, 'foo'),
                $this->createTaxonomyEntry(2, 'bar'),
                TaxonomyServiceInterface::MOVE_POSITION_NEXT,
            ),
        ];
    }

    public function dataProviderForTestInvalidInput(): iterable
    {
        yield 'empty array' => [
            [],
            "Missing 'Entry' element for TaxonomyEntryMove.",
        ];

        yield 'missing entry element' => [
            [
                'sibling' => 2,
                'position' => TaxonomyServiceInterface::MOVE_POSITION_NEXT,
            ],
            "Missing 'Entry' element for TaxonomyEntryMove.",
        ];

        yield 'missing sibling element' => [
            [
                'entry' => 1,
                'position' => TaxonomyServiceInterface::MOVE_POSITION_NEXT,
            ],
            "Missing 'Sibling' element for TaxonomyEntryMove.",
        ];

        yield 'missing position element' => [
            [
                'entry' => 1,
                'sibling' => 2,
            ],
            "Missing 'Position' element for TaxonomyEntryMove.",
        ];
    }
}
