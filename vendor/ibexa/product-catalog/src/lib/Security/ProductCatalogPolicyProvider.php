<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Security;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigBuilderInterface;
use Ibexa\Bundle\Core\DependencyInjection\Security\PolicyProvider\PolicyProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class ProductCatalogPolicyProvider implements PolicyProviderInterface, TranslationContainerInterface
{
    public function addPolicies(ConfigBuilderInterface $configBuilder): void
    {
        $configBuilder->addConfig([
            'commerce' => [
                'region' => null,
                'currency' => null,
            ],
            'product' => [
                'create' => ['Language', 'ProductType'],
                'view' => ['ProductType'],
                'edit' => ['Language', 'ProductType'],
                'delete' => ['ProductType'],
            ],
            'customer_group' => [
                'create' => null,
                'view' => null,
                'edit' => null,
                'delete' => null,
            ],
            'product_type' => [
                'create' => ['ProductType'],
                'view' => null,
                'edit' => ['ProductType'],
                'delete' => null,
            ],
            'catalog' => [
                'create' => null,
                'view' => null,
                'edit' => null,
                'delete' => null,
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message('role.policy.commerce', 'forms'))->setDesc('Commerce'),
            (new Message('role.policy.commerce.all_functions', 'forms'))->setDesc('Commerce / All functions'),
            (new Message('role.policy.commerce.region', 'forms'))->setDesc('Regions / Administrate'),
            (new Message('role.policy.commerce.currency', 'forms'))->setDesc('Currencies / Administrate'),
            (new Message('role.policy.product', 'forms'))->setDesc('Product'),
            (new Message('role.policy.product.all_functions', 'forms'))->setDesc('Product / All functions'),
            (new Message('role.policy.product.create', 'forms'))->setDesc('Product / Create'),
            (new Message('role.policy.product.view', 'forms'))->setDesc('Product / View'),
            (new Message('role.policy.product.edit', 'forms'))->setDesc('Product / Edit'),
            (new Message('role.policy.product.delete', 'forms'))->setDesc('Product / Delete'),
            (new Message('role.policy.customer_group', 'forms'))->setDesc('Customer Group'),
            (new Message('role.policy.customer_group.all_functions', 'forms'))->setDesc('Customer Group / All functions'),
            (new Message('role.policy.customer_group.create', 'forms'))->setDesc('Customer Group / Create'),
            (new Message('role.policy.customer_group.view', 'forms'))->setDesc('Customer Group / View'),
            (new Message('role.policy.customer_group.edit', 'forms'))->setDesc('Customer Group / Edit'),
            (new Message('role.policy.customer_group.delete', 'forms'))->setDesc('Customer Group / Delete'),
            (new Message('role.policy.product_type', 'forms'))->setDesc('Product Type'),
            (new Message('role.policy.product_type.all_functions', 'forms'))->setDesc('Product Type / All functions'),
            (new Message('role.policy.product_type.create', 'forms'))->setDesc('Product Type / Create'),
            (new Message('role.policy.product_type.view', 'forms'))->setDesc('Product Type / View'),
            (new Message('role.policy.product_type.edit', 'forms'))->setDesc('Product Type / Edit'),
            (new Message('role.policy.product_type.delete', 'forms'))->setDesc('Product Type / Delete'),
            (new Message('role.policy.catalog', 'forms'))->setDesc('Catalog'),
            (new Message('role.policy.catalog.all_functions', 'forms'))->setDesc('Catalog / All functions'),
            (new Message('role.policy.catalog.create', 'forms'))->setDesc('Catalog / Create'),
            (new Message('role.policy.catalog.view', 'forms'))->setDesc('Catalog / View'),
            (new Message('role.policy.catalog.edit', 'forms'))->setDesc('Catalog / Edit'),
            (new Message('role.policy.catalog.delete', 'forms'))->setDesc('Catalog / Delete'),
        ];
    }
}
