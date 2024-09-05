<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

final class ServiceCallExecuteStep implements StepInterface
{
    /** @var string */
    public $service;

    /** @var array<mixed> */
    public $arguments;

    /** @var string|null */
    public $method;

    /**
     * @param array<mixed> $arguments
     */
    public function __construct(string $service, array $arguments = [], ?string $method = null)
    {
        $this->service = $service;
        $this->arguments = $arguments;
        $this->method = $method;
    }
}

class_alias(ServiceCallExecuteStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ServiceCallExecuteStep');
