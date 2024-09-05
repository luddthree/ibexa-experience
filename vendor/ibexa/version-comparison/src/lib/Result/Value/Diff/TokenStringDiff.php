<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

class TokenStringDiff extends AbstractDiffValue
{
    /** @var string|null */
    private $token;

    public function __construct(string $status, ?string $token = null)
    {
        $this->status = $status;
        $this->token = $token;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
}

class_alias(TokenStringDiff::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\TokenStringDiff');
