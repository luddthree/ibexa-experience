<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\Gateway\Translation;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\TranslationGatewayInterface;

/**
 * @extends \Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\TranslationGatewayInterface<
 *     array{id: int, attribute_group_id: int, language_id: int, name: string, name_normalized: string}
 * >
 */
interface GatewayInterface extends TranslationGatewayInterface
{
    /**
     * @phpstan-return array<int,array{
     *     id: int,
     *     attribute_group_id: int,
     *     language_id: int,
     *     name: string,
     *     name_normalized: string,
     * }>>
     */
    public function findByAttributeGroupId(int $attributeGroupId): array;

    /**
     * @param int[] $attributeGroupIds
     *
     * @phpstan-return array<array{
     *     id: int,
     *     attribute_group_id: int,
     *     language_id: int,
     *     name: string,
     *     name_normalized: string,
     * }>>
     */
    public function findByAttributeGroupIds(array $attributeGroupIds): array;
}
