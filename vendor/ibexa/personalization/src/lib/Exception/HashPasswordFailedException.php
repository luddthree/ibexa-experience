<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use RuntimeException;

final class HashPasswordFailedException extends RuntimeException
{
    public function __construct(string $algorithm)
    {
        $message = 'Failure to hash password with algorithm: ' . $algorithm;

        parent::__construct($message);
    }
}
