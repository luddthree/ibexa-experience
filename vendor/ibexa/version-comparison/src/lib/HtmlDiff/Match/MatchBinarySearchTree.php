<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Match;

class MatchBinarySearchTree
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Match\Node|null */
    private $root;

    public function add(CommonBlock $match)
    {
        $node = new Node($match);

        $current = $this->root;

        // No node exist, make current root node
        if (!$current) {
            $this->root = $node;

            return;
        }

        while (true) {
            $position = $this->compareMatches($current->value, $match);
            if ($position === -1) {
                if ($current->left) {
                    $current = $current->left;
                } else {
                    $current->left = $node;
                    break;
                }
            } elseif ($position === 1) {
                if ($current->right) {
                    $current = $current->right;
                } else {
                    $current->right = $node;
                    break;
                }
            } else {
                break;
            }
        }
    }

    private function compareMatches(CommonBlock $match1, CommonBlock $match2): int
    {
        if ($match2->endInOld() < $match1->startInOld() && $match2->endInNew() < $match1->startInNew()) {
            return -1;
        }
        if ($match2->startInOld() > $match1->endInOld() && $match2->startInNew() > $match1->endInNew()) {
            return 1;
        }

        return 0;
    }

    public function getRoot(): ?Node
    {
        return $this->root;
    }

    public function toArray(): array
    {
        $nodes = [];

        return $this->inOrder($this->root, $nodes);
    }

    private function inOrder(?Node $node, array &$nodes): array
    {
        if ($node) {
            $this->inOrder($node->left, $nodes);
            $nodes[] = $node->value;
            $this->inOrder($node->right, $nodes);
        }

        return $nodes;
    }
}

class_alias(MatchBinarySearchTree::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Match\MatchBinarySearchTree');
