<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Query;

final class LanguageFilter
{
    /** @var string[] */
    private $languages;

    /** @var bool */
    private $useAlwaysAvailable;

    /** @var bool */
    private $excludeTranslationsFromAlwaysAvailable;

    public function __construct(
        array $languages,
        bool $useAlwaysAvailable,
        bool $excludeTranslationsFromAlwaysAvailable
    ) {
        $this->languages = $languages;
        $this->useAlwaysAvailable = $useAlwaysAvailable;
        $this->excludeTranslationsFromAlwaysAvailable = $excludeTranslationsFromAlwaysAvailable;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function getUseAlwaysAvailable(): bool
    {
        return $this->useAlwaysAvailable;
    }

    public function getExcludeTranslationsFromAlwaysAvailable(): bool
    {
        return $this->excludeTranslationsFromAlwaysAvailable;
    }

    public static function fromArray(array $data): self
    {
        $useAlwaysAvailable = !isset($data['useAlwaysAvailable']) || $data['useAlwaysAvailable'] === true;
        $excludeTranslationsFromAlwaysAvailable = $data['excludeTranslationsFromAlwaysAvailable'] ?? true;

        return new self(
            $data['languages'] ?? [],
            $useAlwaysAvailable,
            $excludeTranslationsFromAlwaysAvailable
        );
    }
}

class_alias(LanguageFilter::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Query\LanguageFilter');
