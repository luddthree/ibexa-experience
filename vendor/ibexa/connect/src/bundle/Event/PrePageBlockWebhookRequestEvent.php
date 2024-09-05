<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Symfony\Contracts\EventDispatcher\Event;

final class PrePageBlockWebhookRequestEvent extends Event
{
    private BlockValue $blockValue;

    /** @var array<string,mixed>|null */
    private ?array $query = null;

    /** @var array<string,mixed>|null */
    private ?array $payload = null;

    public function __construct(BlockValue $blockValue)
    {
        $this->blockValue = $blockValue;
    }

    public function getBlockValue(): BlockValue
    {
        return $this->blockValue;
    }

    /**
     * @phpstan-return array<string,mixed>|null
     */
    public function getQuery(): ?array
    {
        return $this->query;
    }

    /**
     * @param mixed $parameter
     */
    public function addQueryParameter(string $name, $parameter): void
    {
        if (isset($this->query[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Query parameter "%s" is already set.',
                $name,
            ));
        }

        $this->query[$name] = $parameter;
    }

    /**
     * @return array<string,mixed>|null
     */
    public function getPayload(): ?array
    {
        return $this->payload;
    }

    /**
     * @param mixed $value
     */
    public function addPayload(string $name, $value): void
    {
        if (isset($this->payload[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Payload parameter "%s" is already set.',
                $name,
            ));
        }

        $this->payload[$name] = $value;
    }

    /**
     * @phpstan-return 'GET'|'POST'
     */
    public function getRequestMethod(): string
    {
        return isset($this->payload) ? 'POST' : 'GET';
    }
}
