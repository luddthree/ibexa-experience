<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Lexer;

interface TokenizerInterface
{
    /**
     * @return \Ibexa\VersionComparison\HtmlDiff\Token\Token[]
     */
    public function convertHtmlToTokens(string $html): array;
}

class_alias(TokenizerInterface::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Lexer\TokenizerInterface');
