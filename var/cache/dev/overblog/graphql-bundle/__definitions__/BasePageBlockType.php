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
final class BasePageBlockType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'BasePageBlock';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                '_properties' => [
                    'type' => fn() => $services->getType('PageBlockProperties'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value;
                    },
                ],
                'attributes' => [
                    'type' => fn() => Type::listOf($services->getType('PageBlockAttribute')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("PageBlockAttributes", ...[0 => $value, 1 => $context]);
                    },
                ],
                'html' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("PageBlockHtml", ...[0 => $value, 1 => $args, 2 => $context]);
                    },
                ],
            ],
            'interfaces' => fn() => [
                $services->getType('PageBlock'),
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