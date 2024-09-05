<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar;

use Ibexa\Bundle\Calendar\DependencyInjection\Configuration\Parser\CalendarConfigParser;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaCalendarBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');
        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $core->addConfigParser(new CalendarConfigParser());
    }
}

class_alias(IbexaCalendarBundle::class, 'EzSystems\EzPlatformCalendarBundle\EzPlatformCalendarBundle');
