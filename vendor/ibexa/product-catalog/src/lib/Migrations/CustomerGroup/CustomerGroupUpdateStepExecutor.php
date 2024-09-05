<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\Core\Persistence\Content\Language\Handler;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CustomerGroupUpdateStepExecutor extends AbstractStepExecutor
{
    private CustomerGroupServiceInterface $customerGroupService;

    private Handler $languageHandler;

    public function __construct(CustomerGroupServiceInterface $customerGroupService, Handler $languageHandler)
    {
        $this->customerGroupService = $customerGroupService;
        $this->languageHandler = $languageHandler;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step): array
    {
        assert($step instanceof CustomerGroupUpdateStep);

        $criteria = $step->getCriterion();

        $customerGroupQuery = new CustomerGroupQuery($criteria, [], null);
        $customerGroups = $this->customerGroupService->findCustomerGroups($customerGroupQuery);

        $results = [];
        foreach ($customerGroups->getCustomerGroups() as $customerGroup) {
            $names = $step->getNames();
            $names = $this->convertLanguageCodes($names);

            $descriptions = $step->getDescriptions();
            $descriptions = $this->convertLanguageCodes($descriptions);

            $struct = new CustomerGroupUpdateStruct(
                $customerGroup->getId(),
                $step->getIdentifier(),
                $names,
                $descriptions,
                $step->getGlobalPriceRate(),
            );

            $results[] = $this->customerGroupService->updateCustomerGroup($struct);
        }

        return $results;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof CustomerGroupUpdateStep;
    }

    /**
     * @param array<string, string> $translatable
     *
     * @return array<int, string>
     */
    private function convertLanguageCodes(array $translatable): array
    {
        $converted = [];
        foreach ($translatable as $languageCode => $value) {
            $language = $this->languageHandler->loadByLanguageCode($languageCode);
            $converted[$language->id] = $value;
        }

        return $converted;
    }
}
