<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ImageEditor;

use Ibexa\Bundle\ImageEditor\DependencyInjection\Configuration\Parser\ImageEditor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaImageEditorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');

        $core->addConfigParser(new ImageEditor());
    }
}

class_alias(IbexaImageEditorBundle::class, 'Ibexa\Platform\Bundle\ImageEditor\IbexaPlatformImageEditorBundle');
