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
final class TaxonomyTagsType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'TaxonomyTags';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'identifier' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'root' => [
                    'type' => fn() => Type::nonNull($services->getType('TaxonomyEntry')),
                    'description' => 'Root element',
                ],
                'single' => [
                    'type' => fn() => $services->getType('TaxonomyEntry'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("TaxonomyEntry", $args, "tags");
                    },
                    'description' => 'Fetch single Taxonomy Entry using ID, identifier or contentId',
                    'args' => [
                        [
                            'name' => 'id',
                            'type' => Type::int(),
                        ],
                        [
                            'name' => 'identifier',
                            'type' => Type::string(),
                        ],
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                        ],
                    ],
                ],
                'all' => [
                    'type' => fn() => $services->getType('TaxonomyEntryConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("TaxonomyEntryAll", $args, "tags");
                    },
                    'description' => 'Fetch multiple Taxonomy Entries',
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