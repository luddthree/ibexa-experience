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
final class LocationUrlAliasType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'LocationUrlAlias';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'id' => [
                    'type' => Type::string(),
                ],
                'path' => [
                    'type' => Type::string(),
                ],
                'languageCodes' => [
                    'type' => Type::listOf(Type::string()),
                ],
                'alwaysAvailable' => [
                    'type' => Type::boolean(),
                ],
                'isHistory' => [
                    'type' => Type::boolean(),
                ],
                'isCustom' => [
                    'type' => Type::boolean(),
                ],
                'forward' => [
                    'type' => Type::boolean(),
                ],
                'location' => [
                    'type' => fn() => $services->getType('Location'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationById", ...[0 => $value->destination]);
                    },
                ],
            ],
            'interfaces' => fn() => [
                $services->getType('UrlAlias'),
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