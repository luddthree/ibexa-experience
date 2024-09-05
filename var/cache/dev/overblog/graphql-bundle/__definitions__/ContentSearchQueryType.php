<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ContentSearchQueryType extends InputObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ContentSearchQuery';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'ContentTypeIdentifier' => [
                    'type' => fn() => Type::listOf($services->getType('ContentTypeIdentifier')),
                    'description' => 'Content type identifier filter',
                ],
                'ContentTypeId' => [
                    'type' => Type::listOf(Type::string()),
                    'description' => 'Filter on content type id',
                ],
                'Text' => [
                    'type' => Type::string(),
                    'description' => 'Filter on any text from the content item',
                ],
                'Created' => [
                    'type' => fn() => $services->getType('DateInput'),
                    'description' => 'Filter the date the content was initially created on',
                ],
                'Modified' => [
                    'type' => fn() => $services->getType('DateInput'),
                    'description' => 'Filter on the date the content was last modified on',
                ],
                'ParentLocationId' => [
                    'type' => Type::listOf(Type::int()),
                    'description' => 'Filter content based on its parent location id',
                ],
                'Field' => [
                    'type' => fn() => Type::listOf($services->getType('FieldCriterionInput')),
                    'description' => 'Field filter',
                ],
                'SortBy' => [
                    'type' => fn() => $services->getType('SortByOptions'),
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