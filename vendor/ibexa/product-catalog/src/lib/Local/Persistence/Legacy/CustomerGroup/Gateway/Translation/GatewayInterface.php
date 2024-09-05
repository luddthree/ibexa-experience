<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway\Translation;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\TranslationGatewayInterface;

/**
 * @extends \Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\TranslationGatewayInterface<
 *     array{
 *         id: int,
 *         customer_group_id: int,
 *         language_id: int,
 *         name: string,
 *         description: string,
 *     },
 * >
 */
interface GatewayInterface extends TranslationGatewayInterface
{
    /**
     * @param array<string, mixed> $criteria Map of column names to values that will be used as part of WHERE query
     * @param array<string, string>|null $orderBy Map of column names to "ASC" or "DESC", that will be used in SORT query
     *
     * @phpstan-param array<string, "ASC"|"DESC">|null $orderBy
     *
     * @return array<int, array<string, mixed>> Array of results. Should be serializable.
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array;

    /**
     * @param array<string, mixed> $criteria
     */
    public function deleteBy(array $criteria): void;
}
