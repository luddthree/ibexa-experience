<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Token;

use Ibexa\VersionComparison\HtmlDiff\Token\Wrapper\Note;
use Ibexa\VersionComparison\HtmlDiff\Token\Wrapper\TagStackElement;
use Ibexa\VersionComparison\HtmlDiff\Token\Wrapper\TokenListElement;

final class TokenWrapper implements TokenWrapperInterface
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Token\TokenFactory */
    private $tokenFactory;

    public function __construct(TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
    }

    public function wrap(array $tokens, string $tag, string $tagClass): string
    {
        $notes = $this->buildNotes($tokens);

        return $this->combine($notes, $tokens, $tag, $tagClass);
    }

    /**
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Wrapper\Note[] $notes
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $tokens
     */
    private function combine(array $notes, array $tokens, string $tag, string $tagClass)
    {
        $previousIsWrappable = null;
        $lastIndex = 0;
        $combinedTokenList = [];

        foreach ($tokens as $position => $token) {
            if ($notes[$position]->isTagInserted()) {
                $tokens[$position] = $this->wrapTag($token);
            }
            if ($previousIsWrappable === null) {
                $previousIsWrappable = $notes[$position]->isWrappable();
            }
            $currentIsWrappable = $notes[$position]->isWrappable();
            if ($currentIsWrappable !== $previousIsWrappable) {
                $combinedTokenList[] = new TokenListElement(
                    $this->getArraySliceWithingRange($tokens, $lastIndex, $position),
                    $previousIsWrappable
                );
                $lastIndex = $position;
                $previousIsWrappable = $currentIsWrappable;
            }
            if ($position === count($tokens) - 1) {
                $combinedTokenList[] = new TokenListElement(
                    $this->getArraySliceWithingRange($tokens, $lastIndex, $position + 1),
                    $previousIsWrappable
                );
            }
        }
        $toWrap = array_map(static function (TokenListElement $listElement) use ($tag, $tagClass) {
            if ($listElement->isTokenListWrappable()) {
                $attr = '';
                if ($tagClass) {
                    $attr .= ' class="' . $tagClass . '"';
                }

                return '<' . $tag . $attr . '>' . implode('', $listElement->getTokenList()) . '</' . $tag . '>';
            }

            return implode('', $listElement->getTokenList());
        }, $combinedTokenList);

        return implode('', $toWrap);
    }

    private function getArraySliceWithingRange(array $array, int $min, int $max): array
    {
        return array_values(
            array_filter(
                $array,
                static function (int $key) use ($min, $max): bool {
                    return $key >= $min && $key < $max;
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }

    private function wrapTag(Token $token): Token
    {
        return $this->tokenFactory->createToken($token->getString());
    }

    private function isWrappable(Token $token): bool
    {
        $word = $token->getString();

        return $this->isImageTag($word)
            || !$this->isTag($word)
            || $this->isStartOfAtomicTag($word)
            || $this->isVoidTag($word);
    }

    /**
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $tokens
     *
     * @return \Ibexa\VersionComparison\HtmlDiff\Token\Wrapper\Note[]
     */
    private function buildNotes(array $tokens): array
    {
        /** @var \Ibexa\VersionComparison\HtmlDiff\Token\Wrapper\Note[] $notes */
        $notes = [];

        /** @var \Ibexa\VersionComparison\HtmlDiff\Token\Wrapper\TagStackElement[] $tagStack */
        $tagStack = [];
        foreach ($tokens as $position => $token) {
            $notes[] = new Note(
                $this->isWrappable($token),
                false
            );

            $tag = !$this->isVoidTag($token->getString()) && $this->isTag($token->getString());
            $previous = end($tagStack);

            if ($tag) {
                $tagName = $this->getTagName($token->getString());
                if ($previous && '/' . $previous->getTagName() === $this->getTagName($token->getString())) {
                    $noteAtInsertedTag = $notes[$previous->getPosition()];

                    $notes[$previous->getPosition()] = new Note(
                        $noteAtInsertedTag->isWrappable(),
                        true
                    );
                    array_pop($tagStack);
                } else {
                    $tagStack[] = new TagStackElement(
                        $tagName,
                        $position
                    );
                }
            }
        }

        return $notes;
    }

    private function isTag(string $word): bool
    {
        return preg_match('/^\s*<([^!>][^>]*)>\s*$/', $word) === 1;
    }

    private function isVoidTag(string $word): bool
    {
        return preg_match('/^\s*<[^>]+\/>\s*$/', $word) === 1;
    }

    private function getTagName(string $currentWord): string
    {
        preg_match('/^\s*<([^!>][^>]*)>\s*$/', $currentWord, $matches);

        return $matches[1];
    }

    private function isStartOfAtomicTag(string $currentWord): bool
    {
        return preg_match('/^<(iframe|object|math|svg|script|video|head|style)/', $currentWord) === 1;
    }

    private function isImageTag(string $word): bool
    {
        return preg_match('/^<img[\s>]/', $word) === 1;
    }
}

class_alias(TokenWrapper::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Token\TokenWrapper');
