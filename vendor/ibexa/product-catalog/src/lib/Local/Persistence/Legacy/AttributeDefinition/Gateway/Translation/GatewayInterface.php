<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway\Translation;

use Ibexa\Contracts\CorePersistence\Gateway\TranslationGatewayInterface;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\TranslationGatewayInterface<
 *     array{id: int, attribute_definition_id: int, language_id: int, name: string, description: ?string}
 * >
 */
interface GatewayInterface extends TranslationGatewayInterface
{
}
