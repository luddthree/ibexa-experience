<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value\Html;

use Ibexa\VersionComparison\HtmlDiff\DiffBuilderInterface;

class TagAwareHtmlDiffHandler implements HtmlDiffHandler
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\DiffBuilderInterface */
    private $diffBuilder;

    public function __construct(DiffBuilderInterface $diffBuilder)
    {
        $this->diffBuilder = $diffBuilder;
    }

    public function getHtmlDiff(string $htmlA, string $htmlB): string
    {
        return $this->diffBuilder->build($htmlA, $htmlB);
    }
}

class_alias(TagAwareHtmlDiffHandler::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\Html\TagAwareHtmlDiffHandler');
