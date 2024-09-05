<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Token;

interface TokenWrapperInterface
{
    public function wrap(array $tokens, string $tag, string $tagClass): string;
}

class_alias(TokenWrapperInterface::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Token\TokenWrapperInterface');
