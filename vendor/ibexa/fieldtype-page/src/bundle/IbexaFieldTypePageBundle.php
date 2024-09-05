<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\FieldTypePage;

use Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler;
use Ibexa\Bundle\FieldTypePage\DependencyInjection\IbexaFieldTypePageExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaFieldTypePageBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Compiler\LayoutsCompilerPass());
        $container->addCompilerPass(new Compiler\AttributeFormTypeMapperPass());
        $container->addCompilerPass(new Compiler\BlockDefinitionConfigurationCompilerPass());
        $container->addCompilerPass(new Compiler\ReactBlockDefinitionConfigurationCompilerPass());
        $container->addCompilerPass(new Compiler\BlockValidatorsConfigurationCompilerPass());

        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['IbexaGraphQLBundle'])) {
            $container->addCompilerPass(new Compiler\GraphQL\BlockAttributesBuildersPass());
            $container->addCompilerPass(new Compiler\GraphQL\RegisterBlocksAttributesTypesPass());
            $container->addCompilerPass(new Compiler\GraphQL\RegisterBlocksTypesPass());
            $container->addCompilerPass(new Compiler\GraphQL\PageResolverBlocksTypesPass());
            $container->addCompilerPass(new Compiler\GraphQL\RegisterSchedulerBlockTypesPass());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = new IbexaFieldTypePageExtension();
        }

        return $this->extension;
    }
}

class_alias(IbexaFieldTypePageBundle::class, 'EzSystems\EzPlatformPageFieldTypeBundle\EzPlatformPageFieldTypeBundle');
