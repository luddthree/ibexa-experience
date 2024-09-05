<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Handler;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Handler\Handler;
use Ibexa\Core\Base\Exceptions\NotFoundException;

final class DefaultHandlerRegistry implements HandlerRegistry
{
    /** @var \Ibexa\Contracts\Connector\Dam\Handler\Handler[] */
    private $handlers = [];

    public function __construct(iterable $handlers = [])
    {
        foreach ($handlers as $source => $handler) {
            $this->handlers[$source] = $handler;
        }
    }

    public function getHandler(AssetSource $source): Handler
    {
        if (!\array_key_exists($source->getSourceIdentifier(), $this->handlers)) {
            throw new NotFoundException('Handler', $source->getSourceIdentifier());
        }

        return $this->handlers[$source->getSourceIdentifier()];
    }
}

class_alias(DefaultHandlerRegistry::class, 'Ibexa\Platform\Connector\Dam\Handler\DefaultHandlerRegistry');
