<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\DependencyInjection\CompilerPass;

use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PersistenceSerializerCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private const SERIALIZER_SERVICE_ID = 'ibexa.activity_log.persistence.serializer';
    private const NORMALIZER_SERVICE_TAG = 'ibexa.activity_log.persistence.serializer.normalizer';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::SERIALIZER_SERVICE_ID)) {
            return;
        }

        $normalizers = $this->findAndSortTaggedServices(self::NORMALIZER_SERVICE_TAG, $container);
        if (count($normalizers) === 0) {
            throw new RuntimeException(sprintf(
                'You must tag at least one service as "%s" to use the "%s" service.',
                self::NORMALIZER_SERVICE_TAG,
                self::SERIALIZER_SERVICE_ID,
            ));
        }

        $serializerDefinition = $container->getDefinition(self::SERIALIZER_SERVICE_ID);
        $serializerDefinition->replaceArgument(0, $normalizers);
    }
}
