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
final class CreateFormAction extends BaseAction implements ActionInterface
{
    public static function getIdentifier(): string
    {
        return 'create_form';
    }

    public function getConfiguration(): array
    {
        return [
            'name' => self::getIdentifier(),
            'icon_name' => 'qa-form',
            'action' => [
                'udw' => [
                    'title' => $this->translator->trans(
                        /** @Desc("Create form") */
                        'block.quick_actions.tile.create_form.udw.title',
                        [],
                        'ibexa_dashboard'
                    ),
                    'config_name' => 'quick_action_create_form',
                    'context' => ['type' => 'content_create'],
                ],
                'label' => $this->translator->trans(
                    /** @Desc("Form") */
                    'block.quick_actions.tile.form',
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
