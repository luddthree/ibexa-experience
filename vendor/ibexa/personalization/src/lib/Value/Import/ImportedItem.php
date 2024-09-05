<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Import;

use DateTimeImmutable;

final class ImportedItem
{
    /** @var string */
    private $itemTypeName;

    /** @var int */
    private $itemTypeId;

    /** @var string */
    private $language;

    /** @var int */
    private $importedItemsCount;

    /** @var \DateTimeImmutable */
    private $lastUpdateTimestamp;

    public function __construct(
        string $itemTypeName,
        int $itemTypeId,
        string $language,
        int $importedItemsCount,
        DateTimeImmutable $lastUpdateTimestamp
    ) {
        $this->itemTypeName = $itemTypeName;
        $this->itemTypeId = $itemTypeId;
        $this->language = $language;
        $this->importedItemsCount = $importedItemsCount;
        $this->lastUpdateTimestamp = $lastUpdateTimestamp;
    }

    public function getItemTypeName(): string
    {
        return $this->itemTypeName;
    }

    public function getItemTypeId(): int
    {
        return $this->itemTypeId;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getImportedItemsCount(): int
    {
        return $this->importedItemsCount;
    }

    public function getLastUpdateTimestamp(): DateTimeImmutable
    {
        return $this->lastUpdateTimestamp;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['itemTypeName'],
            $properties['itemTypeId'],
            $properties['language'],
            $properties['importedItemsCount'],
            new DateTimeImmutable($properties['lastUpdateTimestamp']),
        );
    }
}

class_alias(ImportedItem::class, 'Ibexa\Platform\Personalization\Value\Import\ImportedItem');
