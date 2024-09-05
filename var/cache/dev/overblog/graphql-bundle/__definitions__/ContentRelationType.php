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
final class ContentRelationType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ContentRelation';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'sourceFieldDefinitionIdentifier' => [
                    'type' => Type::string(),
                ],
                'sourceContent' => [
                    'type' => fn() => $services->getType('DomainContent'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("DomainContentItem", ["id" => $value->sourceContentInfo->id], null);
                    },
                ],
                'destinationContent' => [
                    'type' => fn() => $services->getType('DomainContent'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("DomainContentItem", ["id" => $value->destinationContentInfo->id], null);
                    },
                ],
                'type' => [
                    'type' => fn() => $services->getType('RelationType'),
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