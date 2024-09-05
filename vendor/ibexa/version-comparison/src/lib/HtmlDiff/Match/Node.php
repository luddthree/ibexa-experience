<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Match;

final class Node
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Match\CommonBlock */
    public $value;

    /** @var \Ibexa\VersionComparison\HtmlDiff\Match\Node|null */
    public $left;

    /** @var \Ibexa\VersionComparison\HtmlDiff\Match\Node|null */
    public $right;

    public function __construct(CommonBlock $value, ?Node $left = null, ?Node $right = null)
    {
        $this->value = $value;
        $this->left = $left;
        $this->right = $right;
    }
}

class_alias(Node::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Match\Node');
