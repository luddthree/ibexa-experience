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
final class PageBlockType extends InterfaceType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'PageBlock';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                '_properties' => [
                    'type' => fn() => $services->getType('PageBlockProperties'),
                ],
                'attributes' => [
                    'type' => fn() => Type::listOf($services->getType('PageBlockAttribute')),
                ],
            ],
            'resolveType' => fn($value, $context, $info) => $services->query("PageBlockType", ...[0 => $value, 1 => $context]),
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