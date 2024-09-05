<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Model;

use DateTimeImmutable;

final class MetaData
{
    /** @var \DateTimeImmutable|null */
    private $buildStart;

    /** @var \DateTimeImmutable|null */
    private $buildFinish;

    /** @var array|null */
    private $recommendations;

    /** @var array|null */
    private $importedItems;

    public function __construct(
        ?DateTimeImmutable $buildStart,
        ?DateTimeImmutable $buildFinish,
        ?array $recommendations,
        ?array $importedItems
    ) {
        $this->buildStart = $buildStart;
        $this->buildFinish = $buildFinish;
        $this->recommendations = $recommendations;
        $this->importedItems = $importedItems;
    }

    public function getBuildStart(): ?DateTimeImmutable
    {
        return $this->buildStart;
    }

    public function getBuildFinish(): ?DateTimeImmutable
    {
        return $this->buildFinish;
    }

    public function getRecommendations(): ?array
    {
        return $this->recommendations;
    }

    public function getImportedItems(): ?array
    {
        return $this->importedItems;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            isset($properties['buildStart']) ? new DateTimeImmutable($properties['buildStart']) : null,
            isset($properties['buildFinish']) ? new DateTimeImmutable($properties['buildFinish']) : null,
            array_map([Recommendations::class, 'fromArray'], $properties['recommendationsByType'] ?? []),
            $properties['importedItemsByType'] ?? null
        );
    }
}

class_alias(MetaData::class, 'Ibexa\Platform\Personalization\Value\Model\MetaData');
