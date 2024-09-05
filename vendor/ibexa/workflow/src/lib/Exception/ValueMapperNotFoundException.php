<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Exception;

use Exception;
use InvalidArgumentException;

class ValueMapperNotFoundException extends InvalidArgumentException
{
    public function __construct($matcherType, $code = 0, Exception $previous = null)
    {
        parent::__construct("No MatcherValueMapper found for '$matcherType'", $code, $previous);
    }
}

class_alias(ValueMapperNotFoundException::class, 'EzSystems\EzPlatformWorkflow\Exception\ValueMapperNotFoundException');
