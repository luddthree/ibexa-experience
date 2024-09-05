<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class CatalogCopyStruct extends ValueObject
{
    private string $identifier;

    private ?int $creatorId;

    public function __construct(
        string $identifier,
        ?int $creatorId = null
    ) {
        parent::__construct();

        $this->identifier = $identifier;
        $this->creatorId = $creatorId;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }
}
