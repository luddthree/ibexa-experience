<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\InterfaceType;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ContentAwareInterfaceType extends InterfaceType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ContentAwareInterface';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                '_content' => [
                    'type' => fn() => $services->getType('Content'),
                    'description' => 'Underlying content item',
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