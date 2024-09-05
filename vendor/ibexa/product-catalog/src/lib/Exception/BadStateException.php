<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Exception;

use Exception;
use Ibexa\Core\Base\Exceptions\BadStateException as BaseBadStateException;

final class BadStateException extends BaseBadStateException
{
    public function __construct(
        string $argumentName,
        string $whatIsWrong,
        ?Exception $previous = null
    ) {
        parent::__construct(
            $argumentName,
            $whatIsWrong,
            $previous
        );
    }
}
