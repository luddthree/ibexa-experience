<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ConfigurePersonalizationMenuEvent extends Event
{
    public const SCENARIO_EDIT_SIDEBAR_RIGHT = 'ibexa_personalization.menu_configure.scenario_edit_sidebar_right';
    public const SCENARIO_CREATE_SIDEBAR_RIGHT = 'ibexa_personalization.menu_configure.scenario_create_sidebar_right';
    public const MODEL_EDIT_SIDEBAR_RIGHT = 'ibexa_personalization.menu_configure.model_edit_sidebar_right';
}

class_alias(ConfigurePersonalizationMenuEvent::class, 'Ibexa\Platform\Personalization\Event\ConfigurePersonalizationMenuEvent');
