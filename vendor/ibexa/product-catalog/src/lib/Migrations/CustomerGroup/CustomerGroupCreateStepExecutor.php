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
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CustomerGroupCreateStepExecutor extends AbstractStepExecutor
{
    private CustomerGroupServiceInterface $customerGroupService;

    private Handler $languageHandler;

    public function __construct(CustomerGroupServiceInterface $customerGroupService, Handler $languageHandler)
    {
        $this->customerGroupService = $customerGroupService;
        $this->languageHandler = $languageHandler;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step): CustomerGroupInterface
    {
        assert($step instanceof CustomerGroupCreateStep);

        $names = $step->getNames();
        $names = $this->convertLanguageCodes($names);

        $descriptions = $step->getDescriptions();
        $descriptions = $this->convertLanguageCodes($descriptions);

        $struct = new CustomerGroupCreateStruct(
            $step->getIdentifier(),
            $names,
            $descriptions,
            $step->getGlobalPriceRate(),
        );

        return $this->customerGroupService->createCustomerGroup($struct);
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof CustomerGroupCreateStep;
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
