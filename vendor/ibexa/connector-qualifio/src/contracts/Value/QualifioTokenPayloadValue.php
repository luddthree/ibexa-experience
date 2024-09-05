<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ConnectorQualifio\Value;

final class QualifioTokenPayloadValue
{
    private string $context;

    private string $identifier;

    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct(string $context, string $identifier, $value)
    {
        $this->context = $context;
        $this->identifier = $identifier;
        $this->value = $value;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
