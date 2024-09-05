<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

class HtmlDiff extends AbstractDiffValue
{
    /**
     * @var string
     *
     * Tag or text.
     */
    private $token;

    public function __construct(string $token, string $status)
    {
        $this->token = $token;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}

class_alias(HtmlDiff::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\HtmlDiff');
