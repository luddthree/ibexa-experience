<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\GraphQL;

use Ibexa\FieldTypePage\GraphQL\Resolver\PageResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Sets the registered PageBlock types to the PageResolver.
 */
class PageResolverBlocksTypesPass implements CompilerPassInterface
{
    private const RESOLVER_ID = PageResolver::class;
    private const PAGE_BLOCKS_LIST_FILE_NAME = 'PageBlocksList.types.yaml';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::RESOLVER_ID)) {
            return;
        }

        $pageBlocksIndexFile = $container->getParameter('ibexa.graphql.schema.ibexa_dir') . \DIRECTORY_SEPARATOR . self::PAGE_BLOCKS_LIST_FILE_NAME;

        if (!file_exists($pageBlocksIndexFile)) {
            return;
        }

        $typesMap = $this->getTypesMap($pageBlocksIndexFile);

        $resolverDefinition = $container->getDefinition(self::RESOLVER_ID);
        $resolverDefinition->setArgument('$blocksTypesMap', $typesMap);
        $container->setDefinition(self::RESOLVER_ID, $resolverDefinition);
    }

    /**
     * @param string $pageBlocksIndexFile
     *
     * @return array
     */
    private function getTypesMap(string $pageBlocksIndexFile): array
    {
        $definition = Yaml::parseFile($pageBlocksIndexFile);
        $types = [];
        foreach ($definition['PageBlocksList']['config']['values'] as $typeIdentifier => $enumValue) {
            $types[$typeIdentifier] = $enumValue['value'];
        }

        return $types;
    }
}

class_alias(PageResolverBlocksTypesPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\GraphQL\PageResolverBlocksTypesPass');
