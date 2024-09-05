<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use PHPUnit\Framework\TestCase;

abstract class AbstractTaxonomyEntryTransformerTest extends TestCase
{
    protected TaxonomyServiceInterface $taxonomyService;

    protected function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->taxonomyService->method('loadEntryById')->willReturnMap([
            [1, $this->createEntry(1)],
            [2, $this->createEntry(2)],
            [3, $this->createEntry(3)],
        ]);
    }

    protected function createEntry(int $id): TaxonomyEntry
    {
        return new TaxonomyEntry(
            $id,
            'entry_' . $id,
            'Taxonomy Entry',
            'eng-GB',
            [
                'eng-GB' => 'Taxonomy Entry',
            ],
            null,
            $this->createMock(Content::class),
            'tags',
        );
    }
}
