<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Token;

interface TokenFactoryInterface
{
    public function createToken(string $word): Token;
}

class_alias(TokenFactoryInterface::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Token\TokenFactoryInterface');
