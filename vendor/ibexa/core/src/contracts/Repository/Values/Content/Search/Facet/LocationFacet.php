<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

/**
 * Facet containing counts for content below child locations.
 *
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
class LocationFacet extends Facet
{
    /**
     * An array with location id as key and count of matching content objects which are below this location as value.
     *
     * @var array
     */
    public $entries;
}

class_alias(LocationFacet::class, 'eZ\Publish\API\Repository\Values\Content\Search\Facet\LocationFacet');
