<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Lexer;

use Exception;
use Ibexa\VersionComparison\HtmlDiff\Token\TokenFactoryInterface;

class Tokenizer implements TokenizerInterface
{
    private const CHARACTER_MODE = 'character';
    private const TAG_MODE = 'tag';
    private const ATOMIC_TAG_MODE = 'atomic_tag';
    private const WHITESPACE_MODE = 'whitespace';

    /** @var \Ibexa\VersionComparison\HtmlDiff\Token\TokenFactoryInterface */
    private $tokenFactory;

    public function __construct(
        TokenFactoryInterface $tokenFactory
    ) {
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * @param string $html
     *
     * @return \Ibexa\VersionComparison\HtmlDiff\Token\Token[]
     *
     * @throws \Exception
     */
    public function convertHtmlToTokens(string $html): array
    {
        $listOfCharacters = preg_split('//u', $html);
        $mode = self::CHARACTER_MODE;
        $currentWord = '';
        $currentAtomicTag = '';
        $tokens = [];

        foreach ($listOfCharacters as $character) {
            switch ($mode) {
                case self::TAG_MODE:
                    if ($this->isStartOfAtomicTag($currentWord)) {
                        $mode = self::ATOMIC_TAG_MODE;
                        $currentAtomicTag = $this->getTagName($currentWord);
                        $currentWord .= $character;
                    } elseif ($this->isEndOfTag($character)) {
                        $currentWord .= '>';
                        $tokens[] = $this->tokenFactory->createToken($currentWord);
                        $currentWord = '';
                        $mode = $this->isWhitespace($character) ? self::WHITESPACE_MODE : self::CHARACTER_MODE;
                    } else {
                        $currentWord .= $character;
                    }
                    break;
                case self::ATOMIC_TAG_MODE:
                    if ($this->isEndOfTag($character) && $this->isEndOfAtomicTag($currentWord, $currentAtomicTag)) {
                        $currentWord .= '>';
                        $tokens[] = $this->tokenFactory->createToken($currentWord);
                        $currentWord = '';
                        $currentAtomicTag = '';
                        $mode = self::CHARACTER_MODE;
                    } else {
                        $currentWord .= $character;
                    }
                    break;
                case self::CHARACTER_MODE:
                    if ($this->isStartOfTag($character)) {
                        if (!empty($currentWord)) {
                            $tokens[] = $this->tokenFactory->createToken($currentWord);
                        }
                        $currentWord = '<';
                        $mode = self::TAG_MODE;
                    } elseif ($this->isWhitespace($character)) {
                        if (!empty($currentWord)) {
                            $tokens[] = $this->tokenFactory->createToken($currentWord);
                        }
                        $currentWord = $character;
                        $mode = self::WHITESPACE_MODE;
                    } elseif ($this->isAlphaNumeric($character)) {
                        $currentWord .= $character;
                    } else {
                        if (!empty($currentWord)) {
                            $tokens[] = $this->tokenFactory->createToken($currentWord);
                        }
                        $currentWord = $character;
                    }
                    break;
                case self::WHITESPACE_MODE:
                    if ($this->isStartOfTag($character)) {
                        if (!empty($currentWord)) {
                            $tokens[] = $this->tokenFactory->createToken($currentWord);
                        }
                        $currentWord = '<';
                        $mode = self::TAG_MODE;
                    } elseif ($this->isWhitespace($character)) {
                        $currentWord .= $character;
                    } else {
                        if (!empty($currentWord)) {
                            $tokens[] = $this->tokenFactory->createToken($currentWord);
                        }
                        $currentWord = $character;
                        $mode = self::CHARACTER_MODE;
                    }
                    break;
                default:
                    throw new Exception('Unknown mode ' . $mode);
            }
        }
        if (!empty($currentWord)) {
            $tokens[] = $this->tokenFactory->createToken($currentWord);
        }

        return $tokens;
    }

    private function isEndOfTag(string $character): bool
    {
        return $character === '>';
    }

    private function isStartOfTag(string $character): bool
    {
        return $character === '<';
    }

    private function isWhitespace(string $character): bool
    {
        return preg_match('/^\s*$/', $character) === 1;
    }

    private function isAlphaNumeric(string $character): bool
    {
        return preg_match(' /[\w\#@]+/i', $character) === 1;
    }

    private function isStartOfAtomicTag(string $currentWord): bool
    {
        return preg_match('/^<(iframe|object|math|svg|script|video|head|style)/', $currentWord) === 1;
    }

    private function getTagName(string $currentWord): string
    {
        preg_match('/<(\w+)/', $currentWord, $matches);

        return $matches[1];
    }

    private function isEndOfAtomicTag(string $currentWord, string $currentAtomicTag): bool
    {
        return substr($currentWord, strlen($currentWord) - strlen($currentAtomicTag) - 2) === '</' . $currentAtomicTag;
    }
}

class_alias(Tokenizer::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Lexer\Tokenizer');
