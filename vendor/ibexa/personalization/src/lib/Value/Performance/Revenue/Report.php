<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance\Revenue;

final class Report
{
    private bool $deferred;

    private ?string $name;

    private ?string $content;

    public function __construct(
        bool $deferred,
        ?string $name = null,
        ?string $content = null
    ) {
        $this->deferred = $deferred;
        $this->name = $name;
        $this->content = $content;
    }

    public function isDeferred(): bool
    {
        return $this->deferred;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}

class_alias(Report::class, 'Ibexa\Platform\Personalization\Value\Performance\Revenue\Report');
