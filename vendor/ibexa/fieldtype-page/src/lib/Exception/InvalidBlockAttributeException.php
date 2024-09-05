<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Exception;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;

/**
 * invalid block attribute exception.
 */
class InvalidBlockAttributeException extends InvalidArgumentException
{
    /**
     * @param string $blockType
     * @param string $attribute
     * @param string $message
     * @param \Exception $previous
     */
    public function __construct($blockType, $attribute, $message, Exception $previous = null)
    {
        parent::__construct(
            'Invalid attribute ' . $attribute . ' in ' . $blockType . ' Block. Error message: ' . $message,
            0,
            $previous
        );
    }
}

class_alias(InvalidBlockAttributeException::class, 'EzSystems\EzPlatformPageFieldType\Exception\InvalidBlockAttributeException');
