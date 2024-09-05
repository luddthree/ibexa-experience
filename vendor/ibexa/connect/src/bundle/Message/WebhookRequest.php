<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\Message;

final class WebhookRequest
{
    /** @var non-empty-string */
    private string $url;

    /** @var array<string, mixed> */
    private array $data;

    /**
     * @param non-empty-string $url
     * @param array<string, mixed> $data
     */
    public function __construct(string $url, array $data)
    {
        $this->url = $url;
        $this->data = $data;
    }

    /**
     * @return non-empty-string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
