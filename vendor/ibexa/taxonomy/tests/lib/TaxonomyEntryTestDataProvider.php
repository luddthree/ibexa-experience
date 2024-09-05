<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\Values\Content\Content;

final class TaxonomyEntryTestDataProvider
{
    public static function getRootTaxonomyEntry(?Content $content = null): TaxonomyEntry
    {
        return new TaxonomyEntry(
            3,
            'root',
            'Root',
            'eng-GB',
            ['eng-GB' => 'Root'],
            null,
            $content ?? new Content(),
            'example_taxonomy',
            0,
        );
    }

    public static function getTaxonomyEntry(?TaxonomyEntry $parent, ?Content $content = null): TaxonomyEntry
    {
        return new TaxonomyEntry(
            2,
            'example_entry',
            'Example entry',
            'eng-GB',
            [
                'eng-GB' => 'Example entry',
            ],
            $parent,
            $content ?? new Content(),
            'example_taxonomy',
            1,
        );
    }
}
