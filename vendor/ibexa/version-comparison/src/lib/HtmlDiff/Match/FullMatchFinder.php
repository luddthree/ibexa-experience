<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Match;

final class FullMatchFinder implements MatchFinder
{
    /** @return \Ibexa\VersionComparison\HtmlDiff\Match\CommonBlock[] */
    public function findMatchingBlocks(Segment $entrySegment): array
    {
        $matches = new MatchBinarySearchTree();
        $match = null;
        $segments = [$entrySegment];

        while (count($segments) > 0) {
            /** @var \Ibexa\VersionComparison\HtmlDiff\Match\Segment $segment */
            $segment = array_pop($segments);
            $match = $this->findBestMatch($segment);

            if ($match) {
                // Create segment from unmatched area at the start of current segment
                if ($match->segmentStartInOld() > 0 && $match->segmentStartInNew() > 0) {
                    $leftOldTokens = $this->getArraySliceWithingRange(
                        $segment->getOldTokens(),
                        0,
                        $match->segmentStartInOld()
                    );
                    $leftNewTokens = $this->getArraySliceWithingRange(
                        $segment->getNewTokens(),
                        0,
                        $match->segmentStartInNew()
                    );

                    $segments[] = new Segment(
                        $leftOldTokens,
                        $leftNewTokens,
                        $segment->getOldIndex(),
                        $segment->getNewIndex()
                    );
                }

                // Create segment from unmatched area at the end of current segment
                $rightOldTokens = $this->getArraySliceWithingRange(
                    $segment->getOldTokens(),
                    $match->segmentEndInOld() + 1,
                    array_key_last($segment->getOldTokens()) + 1
                );
                $rightNewTokens = $this->getArraySliceWithingRange(
                    $segment->getNewTokens(),
                    $match->segmentEndInNew() + 1,
                    array_key_last($segment->getNewTokens()) + 1
                );
                if ($rightOldTokens && $rightNewTokens) {
                    $segments[] = new Segment(
                        $rightOldTokens,
                        $rightNewTokens,
                        $segment->getOldIndex() + $match->segmentEndInOld() + 1,
                        $segment->getNewIndex() + $match->segmentEndInNew() + 1
                    );
                }
                $matches->add($match);
            }
        }

        return $matches->toArray();
    }

    private function findBestMatch(Segment $segment): ?CommonBlock
    {
        $lastWhitespaceIndex = null;
        /** @var \Ibexa\VersionComparison\HtmlDiff\Match\CommonBlock|null $bestMatch */
        $bestMatch = null;

        for ($indexInOld = 0; $indexInOld < count($segment->getOldTokens()); ++$indexInOld) {
            $remainingTokensCount = count($segment->getOldTokens()) - $indexInOld;
            if ($bestMatch && $remainingTokensCount < $bestMatch->getLength()) {
                //Nothing to do, best match is longer then remaining tokens.
                break;
            }

            $oldToken = $segment->getOldTokens()[$indexInOld];

            if (!isset($segment->getNewMap()[$oldToken->getHash()])) {
                continue;
            }
            $newTokenLocations = $segment->getNewMap()[$oldToken->getHash()];

            foreach ($newTokenLocations as $indexInNew) {
                $bestMatchLength = $bestMatch ? $bestMatch->getLength() : 0;
                $match = $this->getFullMatch(
                    $segment,
                    $indexInOld,
                    $indexInNew
                );
                if ($match->getLength() > $bestMatchLength) {
                    $bestMatch = $match;
                }
            }
        }

        return $bestMatch;
    }

    private function getFullMatch(
        Segment $segment,
        int $startInOld,
        int $startInNew
    ): CommonBlock {
        $searching = true;
        $currentBestMatchSize = 1;
        $oldIndex = $startInOld + $currentBestMatchSize;
        $newIndex = $startInNew + $currentBestMatchSize;

        while ($searching
            && $oldIndex < count($segment->getOldTokens())
            && $newIndex < count($segment->getNewTokens())
        ) {
            $oldHash = $segment->getOldTokens()[$oldIndex]->getHash();
            $newHash = $segment->getNewTokens()[$newIndex]->getHash();

            if ($oldHash === $newHash) {
                ++$currentBestMatchSize;
                $oldIndex = $startInOld + $currentBestMatchSize;
                $newIndex = $startInNew + $currentBestMatchSize;
            } else {
                $searching = false;
            }
        }

        return new CommonBlock($startInOld, $startInNew, $currentBestMatchSize, $segment);
    }

    private function getArraySliceWithingRange(array $array, int $min, int $max): array
    {
        return array_values(array_filter($array, static function (int $key) use ($min, $max) {
            return $key >= $min && $key < $max;
        }, ARRAY_FILTER_USE_KEY));
    }
}

class_alias(FullMatchFinder::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Match\FullMatchFinder');
