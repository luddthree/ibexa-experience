<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\Gateway;

use Ibexa\Contracts\CorePersistence\Gateway\AbstractTranslationGateway as CoreAbstractTranslationGateway;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\TranslationGatewayInterface;

/**
 * @template T of array
 *
 * @implements \Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\TranslationGatewayInterface<T>
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractTranslationGateway<T>
 */
abstract class AbstractTranslationGateway extends CoreAbstractTranslationGateway implements TranslationGatewayInterface
{
}
