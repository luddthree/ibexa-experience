<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Token;

final class TokenFactory implements TokenFactoryInterface
{
    public function createToken(string $word): Token
    {
        return new Token(
            $word,
            $this->createHash($this->getDataToHash($word))
        );
    }

    private function createHash(string $token): string
    {
        return md5($token);
    }

    private function getDataToHash(string $word): string
    {
        if ($this->isOpeningTag($word)) {
            $word = trim(preg_replace('/\s\s+/', ' ', $word));
        }

        preg_match('/^<img.*src=[\'"]([^"\']*)[\'"].*>$/', $word, $imageMatch);
        if ($imageMatch) {
            return '<img src="' . $imageMatch[1] . '">';
        }

        preg_match('/^<iframe.*src=[\'"]([^"\']*)[\'"].*>$/', $word, $iframeMatch);
        if ($iframeMatch) {
            return '<iframe src="' . $iframeMatch[1] . '">';
        }

        preg_match('/<([^\s>]+)[\s>]/', $word, $tagMatch);
        if ($tagMatch) {
            return '<' . strtolower($tagMatch[1]) . '>';
        }

        return $word;
    }

    private function isOpeningTag(string $word): bool
    {
        return preg_match('#<[^>]+>\\s*#iUu', $word) === 1;
    }
}

class_alias(TokenFactory::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Token\TokenFactory');
