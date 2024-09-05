<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy;

use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase as CoreAbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as GatewayInterface;

/**
 * @template T of array
 *
 * @implements \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<T>
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<T>
 */
abstract class AbstractDoctrineDatabase extends CoreAbstractDoctrineDatabase implements GatewayInterface
{
}
