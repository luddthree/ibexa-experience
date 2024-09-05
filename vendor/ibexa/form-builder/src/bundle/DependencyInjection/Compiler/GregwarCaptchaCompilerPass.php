<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder\DependencyInjection\Compiler;

use Gregwar\CaptchaBundle\Controller\CaptchaController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

final class GregwarCaptchaCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('gregwar_captcha.controller')) {
            return;
        }

        $captchaController = $container->getDefinition('gregwar_captcha.controller');

        if (is_subclass_of(CaptchaController::class, ServiceSubscriberInterface::class)
            && !$captchaController->hasMethodCall('setContainer')
        ) {
            $captchaController->addMethodCall(
                'setContainer',
                [new Reference('service_container')]
            );
        }
    }
}

class_alias(GregwarCaptchaCompilerPass::class, 'EzSystems\EzPlatformFormBuilderBundle\DependencyInjection\Compiler\GregwarCaptchaCompilerPass');
