<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Exception;

use Exception;
use Throwable;

class ValidatorNotFoundException extends Exception
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
                'Could not find attribute validator for type "%s".',
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

class_alias(ValidatorNotFoundException::class, 'EzSystems\EzPlatformFormBuilder\Exception\ValidatorNotFoundException');
