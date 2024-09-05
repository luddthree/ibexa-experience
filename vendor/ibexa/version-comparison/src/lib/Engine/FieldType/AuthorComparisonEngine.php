<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\FieldType;

use Ibexa\Contracts\VersionComparison\Engine\FieldTypeComparisonEngine;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Engine\Value\CollectionComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\FieldType\Author\Author;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\Author\AuthorComparisonResult;
use Ibexa\VersionComparison\Result\FieldType\Author\SingleAuthorComparisonResult;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class AuthorComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringValueComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\CollectionComparisonEngine */
    private $collectionComparisonEngine;

    public function __construct(
        StringComparisonEngine $stringValueComparisonEngine,
        CollectionComparisonEngine $collectionComparisonEngine
    ) {
        $this->stringValueComparisonEngine = $stringValueComparisonEngine;
        $this->collectionComparisonEngine = $collectionComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Author\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Author\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $removedList = [];
        $retainedList = [];
        $addedList = [];

        $collectionComparison = $this->collectionComparisonEngine->compareValues(
            $comparisonDataA->authors,
            $comparisonDataB->authors
        );

        foreach ($collectionComparison->getRemoved()->getCollection() as $removed) {
            $removedList[] = new SingleAuthorComparisonResult(
                $removed->id,
                new StringComparisonResult([
                    new TokenStringDiff(DiffStatus::REMOVED, $removed->name->value),
                ]),
                new StringComparisonResult([
                    new TokenStringDiff(DiffStatus::REMOVED, $removed->email->value),
                ]),
            );
        }

        foreach ($collectionComparison->getUnchanged()->getCollection() as $authorA) {
            $matchedAuthor = $this->findMatchingAuthor($authorA->id, $comparisonDataB->authors->collection);

            $nameComparisonResult = $this->stringValueComparisonEngine->compareValues(
                $authorA->name,
                $matchedAuthor->name
            );
            $emailComparisonResult = $this->stringValueComparisonEngine->compareValues(
                $authorA->email,
                $matchedAuthor->email
            );
            $retainedList[] = new SingleAuthorComparisonResult(
                $authorA->id,
                $nameComparisonResult,
                $emailComparisonResult
            );
        }

        foreach ($collectionComparison->getAdded()->getCollection() as $added) {
            $addedList[] = new SingleAuthorComparisonResult(
                $added->id,
                new StringComparisonResult([
                    new TokenStringDiff(DiffStatus::ADDED, $added->name->value),
                ]),
                new StringComparisonResult([
                    new TokenStringDiff(DiffStatus::ADDED, $added->email->value),
                ]),
            );
        }

        return new AuthorComparisonResult(
            new CollectionComparisonResult(
                new CollectionDiff(DiffStatus::REMOVED, $removedList),
                new CollectionDiff(DiffStatus::UNCHANGED, $retainedList),
                new CollectionDiff(DiffStatus::ADDED, $addedList),
            )
        );
    }

    /**
     * @param int $id
     * @param \Ibexa\VersionComparison\FieldType\Author\Author[] $authors
     */
    private function findMatchingAuthor(int $id, array $authors): ?Author
    {
        foreach ($authors as $author) {
            if ($author->id === $id) {
                return $author;
            }
        }

        return null;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Author\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Author\Value $comparisonDataB
     */
    public function shouldRunComparison(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): bool {
        return $comparisonDataA->authors->collection != $comparisonDataB->authors->collection;
    }
}

class_alias(AuthorComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\AuthorComparisonEngine');
