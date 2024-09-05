<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation;

use Ibexa\Bundle\Segmentation\DependencyInjection\Configuration\Parser\SegmentationParser;
use Ibexa\Segmentation\Permission\SegmentationPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaSegmentationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernelExtension */
        $kernelExtension = $container->getExtension('ibexa');

        $configParsers = $this->getConfigParsers();
        array_walk($configParsers, [$kernelExtension, 'addConfigParser']);
        $kernelExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);

        $kernelExtension->addPolicyProvider(new SegmentationPolicyProvider());
    }

    private function getConfigParsers(): array
    {
        return [
            new SegmentationParser(),
        ];
    }
}

class_alias(IbexaSegmentationBundle::class, 'Ibexa\Platform\Bundle\Segmentation\IbexaPlatformSegmentationBundle');
