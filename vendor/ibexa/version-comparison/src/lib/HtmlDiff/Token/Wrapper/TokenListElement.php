<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Token\Wrapper;

final class TokenListElement
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Token\Token[] */
    private $tokenList;

    /** @var bool */
    private $isTokenListWrappable;

    /**
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $tokenList
     * @param bool $isTokenListWrappable
     */
    public function __construct(array $tokenList, bool $isTokenListWrappable)
    {
        $this->tokenList = $tokenList;
        $this->isTokenListWrappable = $isTokenListWrappable;
    }

    /**
     * @return \Ibexa\VersionComparison\HtmlDiff\Token\Token[]
     */
    public function getTokenList(): array
    {
        return $this->tokenList;
    }

    public function isTokenListWrappable(): bool
    {
        return $this->isTokenListWrappable;
    }
}

class_alias(TokenListElement::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Token\Wrapper\TokenListElement');
