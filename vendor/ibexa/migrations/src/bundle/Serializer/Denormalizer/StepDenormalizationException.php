<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer;

use RuntimeException;
use Throwable;

class StepDenormalizationException extends RuntimeException
{
    /**
     * @param int|string $stepNum
     */
    public function __construct($stepNum, Throwable $previous)
    {
        $message = sprintf(
            'Failed denormalizing Step %s. %s',
            $stepNum,
            $previous->getMessage(),
        );

        parent::__construct($message, $previous->getCode(), $previous);
    }
}

class_alias(StepDenormalizationException::class, 'Ibexa\Platform\Bundle\Migration\Serializer\Denormalizer\StepDenormalizationException');
