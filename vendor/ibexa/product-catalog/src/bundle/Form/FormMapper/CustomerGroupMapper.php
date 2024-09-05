<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\FormMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupUpdateData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerGroupMapper
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * @phpstan-param array{
     *     baseLanguage?: \Ibexa\Contracts\Core\Repository\Values\Content\Language|null,
     *     language: \Ibexa\Contracts\Core\Repository\Values\Content\Language,
     * } $params
     */
    public function mapToFormData(CustomerGroupInterface $customerGroup, array $params): CustomerGroupUpdateData
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $params['language'];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $baseLanguage */
        $baseLanguage = $params['baseLanguage'] ?? null;

        if ($baseLanguage) {
            $customerGroup = $this->customerGroupService->getCustomerGroup(
                $customerGroup->getId(),
                [$baseLanguage->languageCode]
            );
        } else {
            $customerGroup = $this->customerGroupService->getCustomerGroup(
                $customerGroup->getId(),
                [$language->languageCode]
            );
        }

        return CustomerGroupUpdateData::createFromCustomerGroup($customerGroup, $language);
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    private function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setRequired(['language'])
            ->setDefined(['baseLanguage'])
            ->setAllowedTypes('baseLanguage', ['null', Language::class])
            ->setAllowedTypes('language', ['null', Language::class]);
    }
}
