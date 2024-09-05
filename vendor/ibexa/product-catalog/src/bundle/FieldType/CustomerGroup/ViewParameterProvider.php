<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\FieldType\CustomerGroup;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use LogicException;

class ViewParameterProvider implements ParameterProviderInterface
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $customerGroupService)
    {
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field
     *
     * @phpstan-return array{
     *  customer_group: \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface|null,
     *  identifier?: string,
     *  name?: string,
     *  description?: string
     * }
     */
    public function getViewParameters(Field $field): array
    {
        if (!$field->value instanceof Value) {
            throw new LogicException(sprintf(
                '"%s" expects "%s", "%s" received. Check service configuration.',
                __CLASS__,
                Value::class,
                get_class($field->value),
            ));
        }

        $id = $field->value->getId();

        if ($id === null) {
            return [
                'customer_group' => null,
            ];
        }

        $customerGroup = $this->customerGroupService->getCustomerGroup($id);

        return [
            'customer_group' => $customerGroup,
            /** @deprecated since 4.6. Use parameters.customer_group.getIdentifier() directly */
            'identifier' => $customerGroup->getIdentifier(),
            /** @deprecated since 4.6. Use parameters.customer_group.getName() directly */
            'name' => $customerGroup->getName(),
            /** @deprecated since 4.6. Use parameters.customer_group.getDescription() directly */
            'description' => $customerGroup->getDescription(),
        ];
    }
}
