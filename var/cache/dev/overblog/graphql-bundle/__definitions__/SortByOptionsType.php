<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\EnumType;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class SortByOptionsType extends EnumType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'SortByOptions';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'values' => [
                '_contentId' => [
                    'value' => '\\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\SortClause\\ContentId',
                    'description' => 'Sort by content id',
                ],
                '_name' => [
                    'value' => '\\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\SortClause\\ContentName',
                    'description' => 'Sort by content name',
                ],
                '_dateModified' => [
                    'value' => '\\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\SortClause\\DateModified',
                    'description' => 'Sort by last modification date',
                ],
                '_datePublished' => [
                    'value' => '\\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\SortClause\\DatePublished',
                    'description' => 'Sort by initial publication date',
                ],
                '_sectionIdentifier' => [
                    'value' => '\\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\SortClause\\SectionIdentifier',
                    'description' => 'Sort by content section identifier',
                ],
                '_sectionName' => [
                    'value' => '\\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\SortClause\\SectionName',
                    'description' => 'Sort by section name',
                ],
                '_score' => [
                    'value' => '\\Ibexa\\Contracts\\Core\\Repository\\Values\\Content\\Query\\SortClause\\Score',
                    'description' => 'Sort by score',
                ],
                '_desc' => [
                    'value' => 'descending',
                    'description' => 'Reverse the previous sorting option',
                ],
            ],
        ];
        
        parent::__construct($configProcessor->process($config));
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getAliases(): array
    {
        return [self::NAME];
    }
}