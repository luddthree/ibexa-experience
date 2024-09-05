<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Handler;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Handler\Handler;

interface HandlerRegistry
{
    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getHandler(AssetSource $source): Handler;
}

class_alias(HandlerRegistry::class, 'Ibexa\Platform\Connector\Dam\Handler\HandlerRegistry');
