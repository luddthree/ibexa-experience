<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Match;

final class Segment
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Token\Token[] */
    private $oldTokens;

    /** @var \Ibexa\VersionComparison\HtmlDiff\Token\Token[] */
    private $newTokens;

    /** @var array */
    private $oldMap;

    /** @var array */
    private $newMap;

    /** @var int */
    private $oldIndex;

    /** @var int */
    private $newIndex;

    public function __construct(
        array $oldTokens,
        array $newTokens,
        int $oldIndex,
        int $newIndex
    ) {
        $this->oldTokens = $oldTokens;
        $this->newTokens = $newTokens;
        $this->oldMap = $this->createMap($oldTokens);
        $this->newMap = $this->createMap($newTokens);
        $this->oldIndex = $oldIndex;
        $this->newIndex = $newIndex;
    }

    /** @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $tokens */
    private function createMap(array $tokens): array
    {
        $map = [];
        foreach ($tokens as $index => $token) {
            if (isset($map[$token->getHash()])) {
                $map[$token->getHash()][] = $index;
            } else {
                $map[$token->getHash()] = [$index];
            }
        }

        return $map;
    }

    /**
     * @return \Ibexa\VersionComparison\HtmlDiff\Token\Token[]
     */
    public function getOldTokens(): array
    {
        return $this->oldTokens;
    }

    /**
     * @return \Ibexa\VersionComparison\HtmlDiff\Token\Token[]
     */
    public function getNewTokens(): array
    {
        return $this->newTokens;
    }

    public function getOldMap(): array
    {
        return $this->oldMap;
    }

    public function getNewMap(): array
    {
        return $this->newMap;
    }

    public function getOldIndex(): int
    {
        return $this->oldIndex;
    }

    public function getNewIndex(): int
    {
        return $this->newIndex;
    }
}

class_alias(Segment::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Match\Segment');
