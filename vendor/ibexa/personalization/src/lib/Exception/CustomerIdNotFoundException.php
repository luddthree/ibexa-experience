<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use RuntimeException;
use Throwable;

final class CustomerIdNotFoundException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Customer id not found in current request';
        }

        parent::__construct($message, $code, $previous);
    }
}

class_alias(CustomerIdNotFoundException::class, 'Ibexa\Platform\Personalization\Exception\CustomerIdNotFoundException');
