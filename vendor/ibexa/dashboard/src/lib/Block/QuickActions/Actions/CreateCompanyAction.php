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
final class CreateCompanyAction extends BaseAction implements ActionInterface
{
    public static function getIdentifier(): string
    {
        return 'create_company';
    }

    public function getConfiguration(): array
    {
        return [
            'name' => self::getIdentifier(),
            'icon_name' => 'qa-company',
            'action' => [
                'href' => $this->urlGenerator->generate('ibexa.corporate_account.company.create'),
                'label' => $this->translator->trans(
                    /** @Desc("Company") */
                    'block.quick_actions.tile.company',
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
