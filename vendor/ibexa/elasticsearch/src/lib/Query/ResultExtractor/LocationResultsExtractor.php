<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\ResultExtractor;

use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;

final class LocationResultsExtractor extends AbstractResultsExtractor
{
    public const LOCATION_ID_FIELD = 'location_id_id';

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location\Handler */
    private $locationHandler;

    public function __construct(
        LocationHandler $locationHandler,
        FacetResultExtractor $facetResultExtractor,
        AggregationResultExtractor $aggregationResultExtractor,
        bool $skipMissingLocations = true
    ) {
        parent::__construct($facetResultExtractor, $aggregationResultExtractor, $skipMissingLocations);

        $this->locationHandler = $locationHandler;
    }

    protected function loadValueObject(array $document): ValueObject
    {
        return $this->locationHandler->load((int)$document[self::LOCATION_ID_FIELD]);
    }

    public function getExpectedSourceFields(): array
    {
        return [
            self::MATCHED_TRANSLATION_FIELD,
            self::LOCATION_ID_FIELD,
        ];
    }
}

class_alias(LocationResultsExtractor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\ResultExtractor\LocationResultsExtractor');
