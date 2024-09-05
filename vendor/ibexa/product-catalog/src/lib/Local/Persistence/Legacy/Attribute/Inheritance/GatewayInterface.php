<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Inheritance;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;

/**
 * Responsible for persisting Class-Table inherited data.
 *
 * @template T of array
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<T>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    /**
     * Decides if this persister can work with this type of object.
     */
    public function canHandle(string $discriminator): bool;

    /**
     * @param mixed $value
     */
    public function create(
        string $discriminator,
        int $id,
        $value
    ): void;

    /**
     * @param mixed $value
     */
    public function update(
        string $discriminator,
        int $id,
        $value
    ): void;

    /**
     * @return string[]
     */
    public function getDiscriminators(): array;
}
