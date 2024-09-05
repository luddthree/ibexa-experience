<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder;

use Ibexa\Bundle\PageBuilder\DependencyInjection\Compiler\TimelineSerializerPass;
use Ibexa\Bundle\PageBuilder\DependencyInjection\Configuration\Parser\PageBuilder;
use Ibexa\Bundle\PageBuilder\DependencyInjection\Configuration\Parser\PageBuilderForms;
use Ibexa\Bundle\PageBuilder\DependencyInjection\IbexaPageBuilderExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaPageBuilderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\LogicException
     */
    public function build(ContainerBuilder $container)
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');
        $core->addConfigParser(new PageBuilder());
        $core->addConfigParser(new PageBuilderForms());
        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['ezplatform_default_settings.yaml']);

        $container->addCompilerPass(new TimelineSerializerPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = new IbexaPageBuilderExtension();
        }

        return $this->extension;
    }
}

class_alias(IbexaPageBuilderBundle::class, 'EzSystems\EzPlatformPageBuilderBundle\EzPlatformPageBuilderBundle');
