<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Tests;

use Ibexa\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\Service\FieldTypeService;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\ResolverInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Serializer\SerializerInterface;

trait MigrationTestServicesTrait
{
    protected static function getFilesystem(): FilesystemOperator
    {
        return self::getServiceByClassName(
            FilesystemOperator::class,
            'ibexa.migrations.io.flysystem.default_filesystem'
        );
    }

    protected static function getMigrationSerializer(): SerializerInterface
    {
        return self::getServiceByClassName(SerializerInterface::class, 'ibexa.migrations.serializer');
    }

    protected static function getFieldTypeService(): FieldTypeService
    {
        return self::getServiceByClassName(FieldTypeService::class);
    }

    protected static function getReferenceResolver(string $resolver): ResolverInterface
    {
        $resolver = 'ibexa.migrations.reference_definition.resolver.' . $resolver;

        return self::getServiceByClassName(ResolverInterface::class, $resolver);
    }

    protected static function getCollector(): CollectorInterface
    {
        return self::getServiceByClassName(CollectorInterface::class);
    }

    protected static function getMetadataStorage(): MetadataStorage
    {
        return self::getServiceByClassName(MetadataStorage::class);
    }
}
