<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FormBuilder;

use Ibexa\Bundle\FormBuilder\DependencyInjection\Compiler\CaptchaCompilerPass;
use Ibexa\Bundle\FormBuilder\DependencyInjection\Compiler\FormBlockRelationCompilerPass;
use Ibexa\Bundle\FormBuilder\DependencyInjection\Configuration\Parser\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaFormBuilderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\LogicException
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FormBlockRelationCompilerPass());
        $container->addCompilerPass(new CaptchaCompilerPass());

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');
        $core->addConfigParser(new FormBuilder());
        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
    }
}

class_alias(IbexaFormBuilderBundle::class, 'EzSystems\EzPlatformFormBuilderBundle\EzPlatformFormBuilderBundle');
