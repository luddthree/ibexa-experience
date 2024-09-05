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
final class UntypedContentType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'UntypedContent';
    
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
                        return $services->query("ContentTypeById", ...[0 => $value->contentInfo->contentTypeId]);
                    },
                    'description' => 'The item\'s content type',
                ],
                '_content' => [
                    'type' => fn() => $services->getType('Content'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->contentInfo;
                    },
                    'deprecationReason' => 'Renamed to _info',
                    'description' => 'Underlying content info item',
                ],
                '_info' => [
                    'type' => fn() => $services->getType('Content'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->contentInfo;
                    },
                    'description' => 'Underlying content info item',
                ],
                '_location' => [
                    'type' => fn() => $services->getType('Location'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationById", ...[0 => $value->contentInfo->mainLocationId]);
                    },
                    'description' => 'Main location',
                ],
                '_allLocations' => [
                    'type' => fn() => Type::listOf($services->getType('Location')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationsByContentId", ...[0 => $value->contentInfo->id]);
                    },
                    'description' => 'All the locations',
                ],
                '_name' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getName();
                    },
                    'description' => 'The content item\'s name, in the prioritized language(s), based on the object name pattern',
                ],
                '_url' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("MainUrlAlias", ...[0 => $value]);
                    },
                    'description' => 'The content item\'s url alias, based on the main location.',
                ],
                '_thumbnail' => [
                    'type' => fn() => $services->getType('Thumbnail'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("Thumbnail", $value->getThumbnail());
                    },
                ],
                'reason' => [
                    'type' => Type::string(),
                    'resolve' => fn() => 'This content type isn\'t part of the schema.',
                ],
            ],
            'interfaces' => fn() => [
                $services->getType('DomainContent'),
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