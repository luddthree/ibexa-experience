<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\ValueConverterInterface;

class LocationList implements ValueConverterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @param string $storageValue
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public function fromStorageValue(string $storageValue): array
    {
        return $this->locationService->loadLocationList(explode(',', $storageValue));
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location[] $value
     *
     * @return string
     */
    public function toStorageValue($value): string
    {
        return implode(',', array_column($value, 'id'));
    }
}

class_alias(LocationList::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\ValueConverter\LocationList');
