<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ImagePicker;

use Ibexa\Bundle\ImagePicker\DependencyInjection\Compiler\ImagePickerAdminUiOverridePass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaImagePickerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ImagePickerAdminUiOverridePass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
    }
}
