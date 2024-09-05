<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use Ibexa\Contracts\Core\Options\OptionsBag;

interface AttributeDefinitionInterface
{
    public function getId(): int;

    public function getName(): string;

    public function getDescription(): ?string;

    public function getIdentifier(): string;

    public function getType(): AttributeTypeInterface;

    public function getGroup(): AttributeGroupInterface;

    public function getPosition(): int;

    public function getOptions(): OptionsBag;
}
