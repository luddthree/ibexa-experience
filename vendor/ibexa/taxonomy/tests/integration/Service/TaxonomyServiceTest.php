<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\Service;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Tests\Integration\Taxonomy\IbexaKernelTestCase;

final class TaxonomyServiceTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::setAdministratorUser();
    }

    public function testMoveEntry(): void
    {
        $taxonomyService = self::getTaxonomyService();

        $parent = $taxonomyService->loadEntryByIdentifier('parent');
        $this->assertParentIdentifier($parent, 'root');

        $child = $taxonomyService->loadEntryByIdentifier('child');
        $this->assertParentIdentifier($child, 'parent');

        $taxonomyService->moveEntry($child, $taxonomyService->loadEntryByIdentifier('root'));

        $child = $taxonomyService->loadEntryByIdentifier('child');
        $this->assertParentIdentifier($child, 'root');
    }

    private function getTaxonomyParentContentValue(TaxonomyEntry $taxonomyEntry): TaxonomyEntry
    {
        $taxonomyField = $taxonomyEntry->getContent()->getField('parent');
        self::assertNotNull($taxonomyField);

        return $taxonomyField->getValue()->getTaxonomyEntry();
    }

    private function assertParentIdentifier(TaxonomyEntry $taxonomyEntry, string $expectedParent): void
    {
        $parentTaxonomyEntryFromContent = $this->getTaxonomyParentContentValue($taxonomyEntry);
        $parentTaxonomyEntry = $taxonomyEntry->getParent();
        self::assertNotNull($parentTaxonomyEntry);

        self::assertEquals($expectedParent, $parentTaxonomyEntry->getIdentifier());
        self::assertEquals($expectedParent, $parentTaxonomyEntryFromContent->getIdentifier());
    }
}
