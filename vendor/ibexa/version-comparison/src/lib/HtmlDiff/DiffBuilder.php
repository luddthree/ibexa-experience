<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff;

use Ibexa\VersionComparison\HtmlDiff\Lexer\TokenizerInterface;
use Ibexa\VersionComparison\HtmlDiff\Match\CommonBlock;
use Ibexa\VersionComparison\HtmlDiff\Match\MatchFinder;
use Ibexa\VersionComparison\HtmlDiff\Match\Segment;
use Ibexa\VersionComparison\HtmlDiff\Operation\DeleteOperation;
use Ibexa\VersionComparison\HtmlDiff\Operation\EqualOperation;
use Ibexa\VersionComparison\HtmlDiff\Operation\InsertOperation;
use Ibexa\VersionComparison\HtmlDiff\Operation\ReplaceOperation;
use Ibexa\VersionComparison\HtmlDiff\Token\TokenWrapperInterface;

class DiffBuilder implements DiffBuilderInterface
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Match\MatchFinder */
    private $matchFinder;

    /** @var \Ibexa\VersionComparison\HtmlDiff\Lexer\TokenizerInterface */
    private $tokenizer;

    /** @var \Ibexa\VersionComparison\HtmlDiff\Token\TokenWrapper */
    private $tokenWrapper;

    public function __construct(
        MatchFinder $matchFinder,
        TokenizerInterface $tokenizer,
        TokenWrapperInterface $tokenWrapper
    ) {
        $this->matchFinder = $matchFinder;
        $this->tokenizer = $tokenizer;
        $this->tokenWrapper = $tokenWrapper;
    }

    public function build(string $oldVersion, string $newVersion): string
    {
        $oldTokens = $this->tokenizer->convertHtmlToTokens($oldVersion);
        $newTokens = $this->tokenizer->convertHtmlToTokens($newVersion);
        $operations = $this->getOperations($oldTokens, $newTokens);
        $content = '';
        foreach ($operations as $operation) {
            $content .= $operation->wrap($this->tokenWrapper, $oldTokens, $newTokens);
        }

        return $content;
    }

    /**
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $oldTokens
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $newTokens
     *
     * @return \Ibexa\VersionComparison\HtmlDiff\Operation\AbstractOperation[]
     */
    public function getOperations(array $oldTokens, array $newTokens): array
    {
        $positionInOld = $positionInNew = 0;
        $operations = [];
        $segmentToSearch = new Segment(
            $oldTokens,
            $newTokens,
            0,
            0
        );

        $matches = $this->matchFinder->findMatchingBlocks($segmentToSearch);

        // Add empty match at the end to force handling unmatched tails
        $matches[] = new CommonBlock(count($oldTokens), count($newTokens), 0, $segmentToSearch);

        foreach ($matches as $i => $match) {
            $matchStartsAtCurrentPositionInOld = ($positionInOld === $match->startInOld());
            $matchStartsAtCurrentPositionInNew = ($positionInNew === $match->startInNew());

            if (!$matchStartsAtCurrentPositionInOld && !$matchStartsAtCurrentPositionInNew) {
                $operations[] = new ReplaceOperation(
                    new DeleteOperation(
                        $positionInOld,
                        $match->startInOld() - 1,
                        $positionInNew,
                        $match->startInNew() - 1
                    ),
                    new InsertOperation(
                        $positionInOld,
                        $match->startInOld() - 1,
                        $positionInNew,
                        $match->startInNew() - 1
                    )
                );
            } elseif ($matchStartsAtCurrentPositionInOld && !$matchStartsAtCurrentPositionInNew) {
                $operations[] = new InsertOperation(
                    $positionInOld,
                    $match->startInOld() - 1,
                    $positionInNew,
                    $match->startInNew() - 1
                );
            } elseif (!$matchStartsAtCurrentPositionInOld && $matchStartsAtCurrentPositionInNew) {
                $operations[] = new DeleteOperation(
                    $positionInOld,
                    $match->startInOld() - 1,
                    $positionInNew,
                    $match->startInNew() - 1
                );
            }

            if ($match->getLength() !== 0) {
                $operations[] = new EqualOperation(
                    $match->startInOld(),
                    $match->endInOld(),
                    $match->startInNew(),
                    $match->endInNew()
                );
            }

            $positionInOld = $match->endInOld() + 1;
            $positionInNew = $match->endInNew() + 1;
        }

        return $operations;
    }
}

class_alias(DiffBuilder::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\DiffBuilder');
