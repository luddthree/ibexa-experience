<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Exception;

use RuntimeException;
use Throwable;

class AttributeNotFoundException extends RuntimeException
{
    /** @var string */
    private $identifier;

    /**
     * @param string $identifier
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $identifier, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Could not find attribute %s', $identifier), $code, $previous);

        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}

class_alias(AttributeNotFoundException::class, 'EzSystems\EzPlatformFormBuilder\Exception\AttributeNotFoundException');
