<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use Throwable;

final class SubmodelNotFoundException extends NotFoundException
{
    public function __construct(string $name, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf(
            'Could not find Submodel with identifier "%s"',
            $name
        );

        parent::__construct($message, $code, $previous);
    }
}

class_alias(SubmodelNotFoundException::class, 'Ibexa\Platform\Personalization\Exception\SubmodelNotFoundException');
