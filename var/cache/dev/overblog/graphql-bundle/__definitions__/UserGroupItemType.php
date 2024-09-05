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
final class UserGroupItemType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'UserGroupItem';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("relay_globalid_field", $value, $info, null, "DomainContent");
                    },
                    'description' => 'The Content item\'s unique ID.',
                ],
                '_type' => [
                    'type' => fn() => $services->getType('ContentType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentTypeById", ...[0 => $value->getContentInfo()->contentTypeId]);
                    },
                    'description' => 'The item\'s content type',
                ],
                '_contentInfo' => [
                    'type' => fn() => $services->getType('Content'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getContent()->contentInfo;
                    },
                    'description' => 'Underlying content info item',
                ],
                '_location' => [
                    'type' => fn() => $services->getType('Location'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getLocation();
                    },
                    'description' => 'Main location',
                ],
                '_allLocations' => [
                    'type' => fn() => Type::listOf($services->getType('Location')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationsByContentId", ...[0 => $value->getContentInfo()->id]);
                    },
                    'description' => 'All the locations',
                ],
                '_name' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getContent()->getName();
                    },
                    'description' => 'The content item\'s name, in the prioritized language(s), based on the object name pattern',
                ],
                '_url' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemUrlAlias", ...[0 => $value]);
                    },
                    'description' => 'The content item\'s url alias, based on the main location.',
                ],
                '_thumbnail' => [
                    'type' => fn() => $services->getType('Thumbnail'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("Thumbnail", $value->getContent()->getThumbnail());
                    },
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemFieldValue", ...[0 => $value, 1 => "name", 2 => $args]);
                    },
                    'args' => [
                        [
                            'name' => 'language',
                            'type' => fn() => $services->getType('RepositoryLanguage'),
                        ],
                    ],
                ],
                'description' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemFieldValue", ...[0 => $value, 1 => "description", 2 => $args]);
                    },
                    'args' => [
                        [
                            'name' => 'language',
                            'type' => fn() => $services->getType('RepositoryLanguage'),
                        ],
                    ],
                ],
            ],
            'interfaces' => fn() => [
                $services->getType('Item'),
                $services->getType('Node'),
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