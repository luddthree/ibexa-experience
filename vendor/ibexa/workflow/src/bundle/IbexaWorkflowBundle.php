<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Workflow;

use Ibexa\Bundle\Workflow\DependencyInjection\Compiler\SearchPass;
use Ibexa\Bundle\Workflow\DependencyInjection\Configuration\Parser\WorkflowParser;
use Ibexa\Workflow\Security\WorkflowPolicyProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaWorkflowBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SearchPass());

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernelExtension */
        $kernelExtension = $container->getExtension('ibexa');
        $kernelExtension->addPolicyProvider(new WorkflowPolicyProvider());

        $configParsers = $this->getConfigParsers();
        array_walk($configParsers, [$kernelExtension, 'addConfigParser']);
        $kernelExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
    }

    private function getConfigParsers(): array
    {
        return [
            new WorkflowParser(),
        ];
    }

    public function registerCommands(Application $application)
    {
    }
}

class_alias(IbexaWorkflowBundle::class, 'EzSystems\EzPlatformWorkflowBundle\EzPlatformWorkflowBundle');
