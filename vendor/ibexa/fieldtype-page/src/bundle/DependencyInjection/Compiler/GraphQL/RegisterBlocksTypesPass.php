<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\GraphQL;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Registers the BlockAttributes GraphQL types.
 *
 * Since they are only referenced by an interface's resolver, they're not added by default.
 */
class RegisterBlocksTypesPass extends RegisterTypesPass implements CompilerPassInterface
{
    private const TYPES_PATTERN = '[A-Z]*PageBlock.types.yaml';
    private const BLOCK_VIEWS_PATTERN = '/BlockViews$/';

    protected function canProcess(ContainerBuilder $container): bool
    {
        $schemaDir = $container->getParameter('ibexa.graphql.schema.ibexa_dir');

        return file_exists($schemaDir) && is_dir($schemaDir);
    }

    /**
     * @param string $directory path to the directory where GraphQL types are defined (as yaml).
     *
     * @return string[]
     */
    protected function getTypes(ContainerBuilder $container): array
    {
        $directory = $container->getParameter('ibexa.graphql.schema.ibexa_dir');

        $finder = new Finder();
        $types = [];
        foreach ($finder->files()->name(self::TYPES_PATTERN)->in($directory) as $file) {
            $extraTypes = array_filter(
                array_keys(Yaml::parseFile($file->getPathName())),
                static function ($typeName) {
                    return !preg_match(self::BLOCK_VIEWS_PATTERN, $typeName);
                },
                ARRAY_FILTER_USE_BOTH
            );
            $types = array_merge($types, $extraTypes);
        }

        return $types;
    }
}

class_alias(RegisterBlocksTypesPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\GraphQL\RegisterBlocksTypesPass');
