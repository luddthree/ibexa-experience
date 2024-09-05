<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\QueryDSL;

final class GeoDistanceQuery implements Query
{
    /** @var string|null */
    private $fieldName;

    /** @var string|null */
    private $distance;

    /** @var float|null */
    private $lat;

    /** @var float|null */
    private $lon;

    public function __construct(
        ?string $fieldName = null,
        ?string $distance = null,
        ?float $lat = null,
        ?float $lon = null
    ) {
        $this->fieldName = $fieldName;
        $this->distance = $distance;
        $this->lat = $lat;
        $this->lon = $lon;
    }

    public function withFieldName(string $fieldName): self
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function withDistance(?string $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function withLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function withLon(?float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'geo_distance' => [
                'distance' => $this->distance,
                $this->fieldName => [
                    'lat' => $this->lat,
                    'lon' => $this->lon,
                ],
                'ignore_unmapped' => true,
            ],
        ];
    }
}

class_alias(GeoDistanceQuery::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\QueryDSL\GeoDistanceQuery');
