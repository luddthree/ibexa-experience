<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AdminUiFormCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $parameterBag = $container->getParameterBag();

        $contentEditFormTemplates = $parameterBag->get('ibexa.site_access.config.admin_group.admin_ui_forms.content_edit_form_templates');
        $parameterBag->set('ibexa.site_access.config.corporate.admin_ui_forms.content_edit_form_templates', $contentEditFormTemplates);
    }
}
