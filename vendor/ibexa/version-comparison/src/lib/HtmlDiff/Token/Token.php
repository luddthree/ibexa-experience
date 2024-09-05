<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Token;

class Token
{
    /** @var string */
    private $string;

    /** @var string */
    private $hash;

    public function __construct(string $string, string $hash)
    {
        $this->string = $string;
        $this->hash = $hash;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function __toString()
    {
        return $this->getString();
    }
}

class_alias(Token::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Token\Token');
