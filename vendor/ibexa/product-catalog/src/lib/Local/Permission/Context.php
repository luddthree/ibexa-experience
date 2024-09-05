<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;

final class Context implements ContextInterface
{
    private ValueObject $object;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ValueObject[] */
    private array $targets;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject[] $targets
     */
    public function __construct(
        ValueObject $object,
        array $targets = []
    ) {
        $this->object = $object;
        $this->targets = $targets;
    }

    public function getObject(): ValueObject
    {
        return $this->object;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }
}
