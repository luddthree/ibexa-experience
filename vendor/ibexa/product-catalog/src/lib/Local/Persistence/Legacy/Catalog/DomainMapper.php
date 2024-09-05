<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog;

use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\ProductCatalog\Local\Persistence\Values\Catalog as SpiCatalog;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog;

final class DomainMapper implements DomainMapperInterface
{
    private LanguageResolver $languageResolver;

    private LanguageHandlerInterface $languageHandler;

    private CatalogQuerySerializerInterface $serializer;

    public function __construct(
        LanguageHandlerInterface $languageHandler,
        LanguageResolver $languageResolver,
        CatalogQuerySerializerInterface $serializer
    ) {
        $this->languageHandler = $languageHandler;
        $this->languageResolver = $languageResolver;
        $this->serializer = $serializer;
    }

    public function createFromSpi(
        SpiCatalog $spiCatalog,
        User $user,
        ?iterable $prioritizedLanguages = null
    ): Catalog {
        assert(isset($spiCatalog->id));

        $prioritizedLanguages ??= $this->getPrioritizedLanguages();

        $name = $description = null;
        foreach ($prioritizedLanguages as $prioritizedLanguage) {
            $languageId = $prioritizedLanguage->id;

            if ($spiCatalog->hasName($languageId)) {
                $name = $spiCatalog->getName($languageId);

                if ($spiCatalog->hasDescription($languageId)) {
                    $description = $spiCatalog->getDescription($languageId);
                }

                break;
            }
        }

        $languageIds = array_keys($spiCatalog->getNames());
        $languages = [];
        foreach ($this->languageHandler->loadList($languageIds) as $language) {
            $languages[] = $language->languageCode;
        }

        $query = null;
        if ($spiCatalog->query !== '') {
            $query = $this->serializer->deserialize($spiCatalog->query);
        }

        return new Catalog(
            $spiCatalog->id,
            $spiCatalog->identifier,
            $name ?? '',
            $languages,
            $user,
            $spiCatalog->created,
            $spiCatalog->modified,
            $spiCatalog->status,
            $query,
            $description ?? '',
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language[]|iterable
     */
    private function getPrioritizedLanguages(): iterable
    {
        return $this->languageHandler->loadListByLanguageCodes(
            $this->languageResolver->getPrioritizedLanguages()
        );
    }
}
