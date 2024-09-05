<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\CustomField;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalOr;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Elasticsearch\Query\Event\QueryFilterEvent;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LanguageQueryFilter implements EventSubscriberInterface
{
    private const FIELD_LANGUAGES = 'content_language_codes_ms';
    private const FIELD_LANGUAGE = 'meta_indexed_language_code_s';
    private const FIELD_IS_MAIN_LANGUAGE = 'meta_indexed_is_main_translation_b';
    private const FIELD_IS_ALWAYS_AVAILABLE = 'meta_indexed_is_main_translation_and_always_available_b';

    public static function getSubscribedEvents(): array
    {
        return [
            QueryFilterEvent::class => 'onQueryFilterEvent',
        ];
    }

    public function onQueryFilterEvent(QueryFilterEvent $event): void
    {
        $languageCriteria = $this->createLanguageFilter($event->getLanguageFilter());

        // Append language criteria to filter
        $query = $event->getQuery();
        if ($query->filter === null) {
            $query->filter = $languageCriteria;
        } else {
            $query->filter = new LogicalAnd([
                $languageCriteria,
                $query->filter,
            ]);
        }
    }

    private function createLanguageFilter(LanguageFilter $filter): ?Criterion
    {
        if (!empty($filter->getLanguages())) {
            // Get condition for prioritized languages fallback
            $criteria = $this->getLanguagesCriteria($filter->getLanguages());

            // Handle always available fallback if used
            if ($filter->getUseAlwaysAvailable()) {
                $criteria = new LogicalOr([
                    $criteria,
                    $this->getAlwaysAvailableCriteria(
                        $filter->getLanguages(),
                        $filter->getExcludeTranslationsFromAlwaysAvailable()
                    ),
                ]);
            }

            return $criteria;
        }

        // Otherwise search only main languages
        return new CustomField(self::FIELD_IS_MAIN_LANGUAGE, Operator::EQ, true);
    }

    /**
     * Returns criteria for prioritized languages fallback.
     *
     * @param string[] $languageCodes
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion
     */
    private function getLanguagesCriteria(array $languageCodes): Criterion
    {
        $criteria = [];

        foreach ($languageCodes as $languageCode) {
            $criterion = new CustomField(self::FIELD_LANGUAGE, Operator::EQ, $languageCode);

            $excluded = $this->getExcludedLanguageCodes($languageCodes, $languageCode);
            if (!empty($excluded)) {
                $criterion = new LogicalAnd([
                    $criterion,
                    new LogicalNot(
                        new CustomField(self::FIELD_LANGUAGES, Operator::IN, $excluded)
                    ),
                ]);
            }

            $criteria[] = $criterion;
        }

        if (count($criteria) > 1) {
            return new LogicalOr($criteria);
        }

        return $criteria[0];
    }

    /**
     * Returns criteria for always available translation fallback.
     *
     * @param string[] $languageCodes
     * @param bool $excludeTranslationsFromAlwaysAvailable
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion
     */
    private function getAlwaysAvailableCriteria(
        array $languageCodes,
        bool $excludeTranslationsFromAlwaysAvailable
    ): Criterion {
        $excludeOnField = $excludeTranslationsFromAlwaysAvailable
            // Exclude all translations by given languages
            ? self::FIELD_LANGUAGES
            // Exclude only main translation by given languages
            : self::FIELD_LANGUAGE
        ;

        return new LogicalAnd([
            // Include always available main language translations
            new CustomField(self::FIELD_IS_ALWAYS_AVAILABLE, Operator::EQ, true),

            new LogicalNot(
                new CustomField($excludeOnField, Operator::IN, $languageCodes)
            ),
        ]);
    }

    /**
     * Returns a list of language codes to be excluded when matching translation in given
     * $selectedLanguageCode.
     *
     * If $selectedLanguageCode is omitted, all languages will be returned.
     *
     * @param string[] $languageCodes
     * @param string|null $selectedLanguageCode
     *
     * @return string[]
     */
    private function getExcludedLanguageCodes(array $languageCodes, ?string $selectedLanguageCode): array
    {
        $excludedLanguageCodes = [];

        foreach ($languageCodes as $languageCode) {
            if ($selectedLanguageCode !== null && $languageCode === $selectedLanguageCode) {
                break;
            }

            $excludedLanguageCodes[] = $languageCode;
        }

        return $excludedLanguageCodes;
    }
}

class_alias(LanguageQueryFilter::class, 'Ibexa\Platform\ElasticSearchEngine\Query\EventSubscriber\LanguageQueryFilter');
