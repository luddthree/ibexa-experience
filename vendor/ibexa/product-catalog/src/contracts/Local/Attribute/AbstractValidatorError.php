<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Attribute;

abstract class AbstractValidatorError
{
    private ?string $target;

    private string $message;

    /** @var array<string,scalar> */
    private array $parameters;

    /**
     * @param array<string,scalar> $parameters
     */
    public function __construct(?string $target, string $message, array $parameters = [])
    {
        $this->target = $target;
        $this->message = $message;
        $this->parameters = $parameters;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string,scalar>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }
}
