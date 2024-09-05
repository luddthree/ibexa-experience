<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

interface CatalogInterface
{
    public function getId(): int;

    public function getName(): string;

    public function getIdentifier(): string;

    public function getDescription(): ?string;

    public function getCreator(): User;

    public function getCreated(): int;

    public function getModified(): int;

    public function getQuery(): CriterionInterface;

    public function getStatus(): string;
}
