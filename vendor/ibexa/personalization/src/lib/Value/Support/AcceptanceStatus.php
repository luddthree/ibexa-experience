<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Support;

final class AcceptanceStatus
{
    /** @var bool */
    private $isAccepted;

    /** @var ?string */
    private $acceptor;

    public function __construct(bool $isAccepted, ?string $acceptor = null)
    {
        $this->isAccepted = $isAccepted;
        $this->acceptor = $acceptor;
    }

    public function isAccepted(): bool
    {
        return $this->isAccepted;
    }

    public function getAcceptor(): ?string
    {
        return $this->acceptor;
    }

    public static function fromArray(array $properties): self
    {
        return new self(
            (bool)$properties['isAccepted'],
            $properties['acceptor'] ?? null
        );
    }
}

class_alias(AcceptanceStatus::class, 'Ibexa\Platform\Personalization\Value\Support\AcceptanceStatus');
