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
final class CreateCatalogAction extends BaseAction implements ActionInterface
{
    public static function getIdentifier(): string
    {
        return 'create_catalog';
    }

    public function getConfiguration(): array
    {
        return [
            'name' => self::getIdentifier(),
            'icon_name' => 'qa-catalog',
            'action' => [
                'href' => $this->urlGenerator->generate('ibexa.product_catalog.catalog.create'),
                'label' => $this->translator->trans(
                    /** @Desc("Catalog") */
                    'block.quick_actions.tile.catalog',
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
