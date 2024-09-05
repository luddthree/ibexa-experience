<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Exception;

use Ibexa\FormBuilder\FieldType\Field\FieldHandlerInterface;
use RuntimeException;
use Throwable;

class FieldHandlerNotFoundException extends RuntimeException
{
    /** @var string */
    private $type;

    /**
     * @param string $type
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $type, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'Could not find %s for type %s',
            FieldHandlerInterface::class,
            $type
        ), $code, $previous);

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}

class_alias(FieldHandlerNotFoundException::class, 'EzSystems\EzPlatformFormBuilder\Exception\FieldHandlerNotFoundException');
