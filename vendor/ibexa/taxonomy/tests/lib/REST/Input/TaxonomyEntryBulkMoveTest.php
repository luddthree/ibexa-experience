<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\REST\Input;

use Ibexa\Contracts\Rest\Input\Parser;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Taxonomy\REST\Input\Parser\TaxonomyEntryBulkMove;
use Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryBulkMove as TaxonomyEntryBulkMoveValue;
use Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryMove as TaxonomyEntryMoveValue;

final class TaxonomyEntryBulkMoveTest extends AbstractInputParserTest
{
    /** @var \Ibexa\Taxonomy\REST\Input\Parser\TaxonomyEntryBulkMove */
    protected Parser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $entryAnimal = $this->createTaxonomyEntry(1, 'Animal');
        $entryCar = $this->createTaxonomyEntry(2, 'Car');
        $entryDrink = $this->createTaxonomyEntry(3, 'Drink');
        $entryFood = $this->createTaxonomyEntry(4, 'Food');

        $this->taxonomyService
            ->method('loadEntryById')
            ->withConsecutive([1], [2], [3], [4])
            ->willReturnOnConsecutiveCalls(
                $entryAnimal,
                $entryCar,
                $entryDrink,
                $entryFood,
            );

        $this->parsingDispatcher
            ->method('parse')
            ->withConsecutive(
                [
                    [
                        'entry' => 1,
                        'sibling' => 2,
                        'position' => 'prev',
                    ],
                    'application/vnd.ibexa.api.TaxonomyEntryMove',
                ],
                [
                    [
                        'entry' => 3,
                        'sibling' => 4,
                        'position' => 'next',
                    ],
                    'application/vnd.ibexa.api.TaxonomyEntryMove',
                ],
            )
            ->willReturnOnConsecutiveCalls(
                new TaxonomyEntryMoveValue(
                    $entryAnimal,
                    $entryCar,
                    TaxonomyServiceInterface::MOVE_POSITION_PREV,
                ),
                new TaxonomyEntryMoveValue(
                    $entryDrink,
                    $entryFood,
                    TaxonomyServiceInterface::MOVE_POSITION_NEXT,
                ),
            );
    }

    protected function getParserUnderTest(): Parser
    {
        return new TaxonomyEntryBulkMove();
    }

    public function dataProviderForTestValidInput(): iterable
    {
        yield [
            [
                'entries' => [
                    [
                        'entry' => 1,
                        'sibling' => 2,
                        'position' => 'prev',
                    ],
                    [
                        'entry' => 3,
                        'sibling' => 4,
                        'position' => 'next',
                    ],
                ],
            ],
            new TaxonomyEntryBulkMoveValue([
                new TaxonomyEntryMoveValue(
                    $this->createTaxonomyEntry(1, 'Animal'),
                    $this->createTaxonomyEntry(2, 'Car'),
                    TaxonomyServiceInterface::MOVE_POSITION_PREV,
                ),
                new TaxonomyEntryMoveValue(
                    $this->createTaxonomyEntry(3, 'Drink'),
                    $this->createTaxonomyEntry(4, 'Food'),
                    TaxonomyServiceInterface::MOVE_POSITION_NEXT,
                ),
            ]),
        ];
    }

    public function dataProviderForTestInvalidInput(): iterable
    {
        yield 'empty array' => [
            [],
            "Missing 'Entries' element for TaxonomyEntryBulkMove.",
        ];
    }
}
