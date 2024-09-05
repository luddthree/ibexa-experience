<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Match;

interface MatchFinder
{
    /** @return \Ibexa\VersionComparison\HtmlDiff\Match\CommonBlock[] */
    public function findMatchingBlocks(Segment $segment): array;
}

class_alias(MatchFinder::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Match\MatchFinder');
