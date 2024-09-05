<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Exception;

use Ibexa\Contracts\FormBuilder\FieldType\Field\FieldMapperInterface;
use RuntimeException;
use Throwable;

class FieldMapperNotFoundException extends RuntimeException
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
            FieldMapperInterface::class,
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

class_alias(FieldMapperNotFoundException::class, 'EzSystems\EzPlatformFormBuilder\Exception\FieldMapperNotFoundException');
