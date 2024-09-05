<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Block\QuickActions\Actions;

use Ibexa\Contracts\Dashboard\Block\QuickActions\ActionInterface;
use JMS\TranslationBundle\Annotation\Desc;

/**
 * @internal
 */
final class CreateProductAction extends BaseAction implements ActionInterface
{
    public static function getIdentifier(): string
    {
        return 'create_product';
    }

    public function getConfiguration(): array
    {
        return [
            'name' => self::getIdentifier(),
            'icon_name' => 'qa-product',
            'action' => [
                'href' => $this->urlGenerator->generate('ibexa.product_catalog.product.list', [
                    '_fragment' => 'pre-create-product',
                ]),
                'label' => $this->translator->trans(
                    /** @Desc("Product") */
                    'block.quick_actions.tile.product',
                    [],
                    'ibexa_dashboard'
                ),
                'sublabel' => $this->translator->trans(
                    /** @Desc("Create") */
                    'block.quick_actions.tile.create',
                    [],
                    'ibexa_dashboard'
                ),
            ],
        ];
    }
}
