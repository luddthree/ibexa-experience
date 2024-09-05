<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\GraphQL;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Registers the BlockAttributes GraphQL types.
 *
 * Since they are only referenced by an interface's resolver, they're not added by default.
 */
class RegisterBlocksAttributesTypesPass extends RegisterTypesPass implements CompilerPassInterface
{
    private const BLOCKS_ATTRIBUTES_INDEX_FILE_NAME = 'PageBlocksList.types.yaml';
    private const BLOCKS_ATTRIBUTES_FILE_NAME = 'PageBlockAttributes.types.yaml';

    protected function canProcess(ContainerBuilder $container): bool
    {
        $eZPlatformSchemaDir = $container->getParameter('ibexa.graphql.schema.ibexa_dir');
        $blocksAttributesIndexFilePath = $eZPlatformSchemaDir . \DIRECTORY_SEPARATOR . self::BLOCKS_ATTRIBUTES_INDEX_FILE_NAME;
        $blocksAttributesConcreteTypesFilePath = \dirname(__DIR__, 3) . '/Resources/config/graphql/' . self::BLOCKS_ATTRIBUTES_FILE_NAME;

        if (!file_exists($blocksAttributesIndexFilePath) || !file_exists($blocksAttributesConcreteTypesFilePath)) {
            return false;
        }

        return true;
    }

    protected function getTypes(ContainerBuilder $container): array
    {
        $eZPlatformSchemaDir = $container->getParameter('ibexa.graphql.schema.ibexa_dir');
        $blocksAttributesIndexFilePath = $eZPlatformSchemaDir . \DIRECTORY_SEPARATOR . self::BLOCKS_ATTRIBUTES_INDEX_FILE_NAME;
        $blocksAttributesConcreteTypesFilePath = \dirname(__DIR__, 3) . '/Resources/config/graphql/' . self::BLOCKS_ATTRIBUTES_FILE_NAME;

        return array_merge(
            $this->getDefinedTypes($blocksAttributesIndexFilePath),
            $this->getDefinedTypes($blocksAttributesConcreteTypesFilePath)
        );
    }

    /**
     * @param string $blocksAttributesIndexFilePath
     *
     * @return string[]
     */
    private function getDefinedTypes(string $blocksAttributesIndexFilePath): array
    {
        return array_keys(Yaml::parseFile($blocksAttributesIndexFilePath));
    }
}

class_alias(RegisterBlocksAttributesTypesPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\GraphQL\RegisterBlocksAttributesTypesPass');
