<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class WorkflowActionResult extends ValueObject
{
    /** @var array */
    protected $results;

    public function __construct(array $results = [])
    {
        $this->results = $results;
    }

    public function set(string $actionIdentifier, $value): void
    {
        if ($value !== null && !is_scalar($value)) {
            throw new InvalidArgumentException('value', self::class . ' accepts only scalar or null values.');
        }

        $this->results[$actionIdentifier] = $value;
    }

    public function get(string $actionIdentifier, $default = null)
    {
        return $this->results[$actionIdentifier] ?? $default;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}

class_alias(WorkflowActionResult::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowActionResult');
