<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ItemType extends InterfaceType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'Item';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                '_contentInfo' => [
                    'type' => fn() => $services->getType('Content'),
                    'description' => 'Item\'s content info',
                ],
                '_type' => [
                    'type' => fn() => $services->getType('ContentType'),
                    'description' => 'Item\'s content type',
                ],
                '_location' => [
                    'type' => fn() => $services->getType('Location'),
                    'description' => 'The content\'s main location',
                ],
                '_allLocations' => [
                    'type' => fn() => Type::listOf($services->getType('Location')),
                    'description' => 'All the content\'s locations',
                ],
                '_name' => [
                    'type' => Type::string(),
                    'description' => 'The content item\'s name, in the prioritized language(s), based on the object name pattern',
                ],
                '_url' => [
                    'type' => Type::string(),
                    'description' => 'The content item\'s url alias for the current location.',
                ],
                '_thumbnail' => [
                    'type' => fn() => $services->getType('Thumbnail'),
                ],
            ],
            'resolveType' => fn($value, $context, $info) => $services->query("ItemType", ...[0 => $value]),
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