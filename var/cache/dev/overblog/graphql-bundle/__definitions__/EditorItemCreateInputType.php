<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class EditorItemCreateInputType extends InputObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'EditorItemCreateInput';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'firstName' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'lastName' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'userAccount' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'image' => [
                    'type' => fn() => $services->getType('ImageFieldInput'),
                ],
                'signature' => [
                    'type' => Type::string(),
                ],
                'position' => [
                    'type' => Type::string(),
                ],
                'department' => [
                    'type' => Type::string(),
                ],
                'location' => [
                    'type' => Type::string(),
                ],
                'phone' => [
                    'type' => Type::string(),
                ],
                'linkedIn' => [
                    'type' => Type::string(),
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