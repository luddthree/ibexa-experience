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
final class VirtualUrlAliasType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'VirtualUrlAlias';
    
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
                'url' => [
                    'type' => Type::string(),
                    'description' => 'The aliased URL',
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