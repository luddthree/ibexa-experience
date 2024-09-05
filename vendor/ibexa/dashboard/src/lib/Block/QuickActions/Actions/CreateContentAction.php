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
final class CreateContentAction extends BaseAction implements ActionInterface
{
    public static function getIdentifier(): string
    {
        return 'create_content';
    }

    public function getConfiguration(): array
    {
        return [
            'name' => self::getIdentifier(),
            'icon_name' => 'qa-content',
            'action' => [
                'udw' => [
                    'title' => $this->translator->trans(
                        /** @Desc("Create content") */
                        'block.quick_actions.tile.create_content.udw.title',
                        [],
                        'ibexa_dashboard'
                    ),
                    'config_name' => 'create',
                    'context' => ['type' => 'content_create'],
                ],
                'label' => $this->translator->trans(
                    /** @Desc("Content") */
                    'block.quick_actions.tile.content',
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
