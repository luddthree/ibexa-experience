<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CaptchaCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $resources = $container->getParameter('twig.form.resources');

        $container->setParameter(
            'twig.form.resources',
            array_merge(['@ibexadesign/fields/captcha.html.twig'], $resources)
        );
    }
}

class_alias(CaptchaCompilerPass::class, 'EzSystems\EzPlatformFormBuilderBundle\DependencyInjection\Compiler\CaptchaCompilerPass');
