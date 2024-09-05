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
final class SectionType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'Section';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An Ibexa repository section.',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The Section\'s unique ID.',
                ],
                'identifier' => [
                    'type' => Type::string(),
                    'description' => 'The Section\'s system identifier.',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'The Section\'s name.',
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