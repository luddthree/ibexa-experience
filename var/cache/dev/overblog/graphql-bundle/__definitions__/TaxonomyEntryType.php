<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class TaxonomyEntryType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'TaxonomyEntry';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                '_content' => [
                    'type' => fn() => $services->getType('Content'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->content->contentInfo;
                    },
                    'description' => 'Underlying content item',
                ],
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Unique Taxonomy Entry ID',
                ],
                'identifier' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Unique Taxonomy Entry string identifier',
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The computed name (via name schema) in the main language of the Content item.',
                ],
                'mainLanguageCode' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'names' => [
                    'type' => Type::listOf(Type::string()),
                ],
                'parent' => [
                    'type' => fn() => $services->getType('TaxonomyEntry'),
                    'description' => 'Parent Taxonomy Entry',
                ],
                'taxonomy' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'children' => [
                    'type' => fn() => $services->getType('TaxonomyEntryConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("TaxonomyEntryChildren", $args, $value->id);
                    },
                    'description' => 'Children Taxonomy Entries',
                    'args' => [
                        [
                            'name' => 'after',
                            'type' => Type::string(),
                        ],
                        [
                            'name' => 'first',
                            'type' => Type::int(),
                        ],
                        [
                            'name' => 'before',
                            'type' => Type::string(),
                        ],
                        [
                            'name' => 'last',
                            'type' => Type::int(),
                        ],
                    ],
                ],
                'level' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Taxonomy Entry Depth',
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