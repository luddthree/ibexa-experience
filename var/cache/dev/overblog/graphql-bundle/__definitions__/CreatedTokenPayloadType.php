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
final class CreatedTokenPayloadType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'CreatedTokenPayload';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'token' => [
                    'type' => Type::string(),
                ],
                'message' => [
                    'type' => Type::string(),
                    'description' => 'The reason why authentication has failed, if it has',
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