<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Exception\Registry;

use Exception;
use Throwable;

class AttributeValidatorNotFoundException extends Exception
{
    /** @var string */
    private $type;

    /**
     * @param string $type
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($type, int $code = 0, Throwable $previous = null)
    {
        $this->type = $type;

        parent::__construct(
            sprintf(
                'Attribute validator for type "%s" was not found.',
                $type
            ),
            $code,
            $previous
        );
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}

class_alias(AttributeValidatorNotFoundException::class, 'EzSystems\EzPlatformPageFieldType\Exception\Registry\AttributeValidatorNotFoundException');
