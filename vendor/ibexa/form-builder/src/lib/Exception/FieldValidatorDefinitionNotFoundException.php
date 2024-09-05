<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Exception;

use RuntimeException;

class FieldValidatorDefinitionNotFoundException extends RuntimeException
{
    /** @var string */
    private $identifier;

    /**
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
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

class_alias(FieldValidatorDefinitionNotFoundException::class, 'EzSystems\EzPlatformFormBuilder\Exception\FieldValidatorDefinitionNotFoundException');
