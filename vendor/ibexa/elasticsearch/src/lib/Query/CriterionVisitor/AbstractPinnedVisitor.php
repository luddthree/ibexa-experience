<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Repository\Values\Content\Query\Pinned;

abstract class AbstractPinnedVisitor implements CriterionVisitor
{
    final public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Pinned;
    }

    final public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        return [
            'pinned' => [
                'ids' => $this->getDocumentIds(
                    $criterion->value,
                    $languageFilter
                ),
                'organic' => $dispatcher->visit(
                    $dispatcher,
                    $criterion->getOrganicCriteria(),
                    $languageFilter
                ),
            ],
        ];
    }

    abstract protected function getDocumentId(int $id, string $languageCode): string;

    abstract protected function getAlwaysAvailableLanguage(int $id): ?string;

    private function getDocumentIds(iterable $ids, LanguageFilter $languageFilter): array
    {
        $documentIds = [];

        foreach ($ids as $contentId) {
            foreach ($languageFilter->getLanguages() as $languageCode) {
                $documentIds[] = $this->getDocumentId($contentId, $languageCode);
            }

            if ($languageFilter->getUseAlwaysAvailable()) {
                $alwaysAvailableLanguage = $this->getAlwaysAvailableLanguage($contentId);
                if ($alwaysAvailableLanguage !== null) {
                    $documentIds[] = $this->getDocumentId($contentId, $alwaysAvailableLanguage);
                }
            }
        }

        return array_unique($documentIds);
    }
}

class_alias(AbstractPinnedVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\AbstractPinnedVisitor');
