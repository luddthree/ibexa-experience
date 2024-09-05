<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Mapper;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry as PersistenceTaxonomyEntry;

interface EntryDomainMapperInterface
{
    public function buildDomainObjectFromPersistence(PersistenceTaxonomyEntry $entry, ?TaxonomyEntry $parentEntry): TaxonomyEntry;
}
