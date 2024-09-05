<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup;

use Ibexa\Contracts\Core\Persistence\Content\Language\Handler as LanguageHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup as SpiCustomerGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup;

final class DomainMapper implements DomainMapperInterface
{
    private LanguageHandlerInterface $languageHandler;

    private LanguageResolver $languageResolver;

    public function __construct(
        LanguageHandlerInterface $languageHandler,
        LanguageResolver $languageResolver
    ) {
        $this->languageHandler = $languageHandler;
        $this->languageResolver = $languageResolver;
    }

    public function createFromSpi(
        SpiCustomerGroup $spiCustomerGroup,
        ?iterable $prioritizedLanguages = null
    ): CustomerGroup {
        assert(isset($spiCustomerGroup->id));
        $prioritizedLanguages ??= $this->getPrioritizedLanguages();

        $name = $description = null;
        foreach ($prioritizedLanguages as $prioritizedLanguage) {
            $languageId = $prioritizedLanguage->id;

            if ($spiCustomerGroup->hasName($languageId)) {
                $name = $spiCustomerGroup->getName($languageId);

                if ($spiCustomerGroup->hasDescription($languageId)) {
                    $description = $spiCustomerGroup->getDescription($languageId);
                }

                break;
            }
        }

        $languageIds = array_keys($spiCustomerGroup->getNames());
        $languages = [];
        foreach ($this->languageHandler->loadList($languageIds) as $language) {
            $languages[] = $language->languageCode;
        }

        return new CustomerGroup(
            $spiCustomerGroup->id,
            $spiCustomerGroup->identifier,
            $name ?? '',
            $description ?? '',
            $spiCustomerGroup->globalPriceRate,
            $languages,
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
