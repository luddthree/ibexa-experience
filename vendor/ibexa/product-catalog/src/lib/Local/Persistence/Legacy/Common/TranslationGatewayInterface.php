<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Common;

use Ibexa\Contracts\CorePersistence\Gateway\TranslationGatewayInterface as CoreTranslationGatewayInterface;

/**
 * @template T
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\TranslationGatewayInterface<T>
 */
interface TranslationGatewayInterface extends CoreTranslationGatewayInterface
{
}
