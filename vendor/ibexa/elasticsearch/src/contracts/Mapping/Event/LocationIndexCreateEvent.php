<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Mapping\Event;

use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Elasticsearch\Mapping\LocationDocument;
use Symfony\Contracts\EventDispatcher\Event;

final class LocationIndexCreateEvent extends Event
{
    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location */
    private $location;

    /** @var \Ibexa\Contracts\Elasticsearch\Mapping\LocationDocument */
    private $document;

    public function __construct(Location $location, LocationDocument $document)
    {
        $this->location = $location;
        $this->document = $document;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getDocument(): LocationDocument
    {
        return $this->document;
    }
}

class_alias(LocationIndexCreateEvent::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Mapping\Event\LocationIndexCreateEvent');
