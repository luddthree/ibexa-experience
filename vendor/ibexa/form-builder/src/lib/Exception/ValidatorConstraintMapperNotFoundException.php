<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Exception;

use Exception;
use Throwable;

class ValidatorConstraintMapperNotFoundException extends Exception
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
                'Could not find ValidatorConstraintMapper for type "%s". Did you register and tag the service?',
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

class_alias(ValidatorConstraintMapperNotFoundException::class, 'EzSystems\EzPlatformFormBuilder\Exception\ValidatorConstraintMapperNotFoundException');
