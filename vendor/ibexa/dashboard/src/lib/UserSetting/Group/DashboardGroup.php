<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\UserSetting\Group;

use Ibexa\User\UserSetting\Group\AbstractGroup;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DashboardGroup extends AbstractGroup
{
    private TranslatorInterface $translator;

    /**
     * @param array<string, \Ibexa\Contracts\User\UserSetting\ValueDefinitionInterface> $values
     */
    public function __construct(
        TranslatorInterface $translator,
        array $values = []
    ) {
        $this->translator = $translator;
        parent::__construct($values);
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Dashboard") */
            'settings.group.dashboard.name',
            [],
            'ibexa_user_settings'
        );
    }

    public function getDescription(): string
    {
        return $this->translator->trans(
            /** @Desc("") */
            'settings.group.dashboard.description',
            [],
            'ibexa_user_settings'
        );
    }
}
