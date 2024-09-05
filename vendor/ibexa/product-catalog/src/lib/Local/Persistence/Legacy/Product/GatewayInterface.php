<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Product;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;

/**
 * @phpstan-type Data array{
 *     id: int,
 *     code: string,
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    public function insert(string $code, ?int $baseProductId = null): int;

    public function update(string $originalCode, ?string $newCode, ?bool $isPublished): void;

    /**
     * @phpstan-return Data|null
     */
    public function findOneByCode(string $code): ?array;
}
