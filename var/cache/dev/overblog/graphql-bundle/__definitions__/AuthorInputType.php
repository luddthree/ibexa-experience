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
final class AuthorInputType extends InputObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'AuthorInput';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'id' => [
                    'type' => Type::int(),
                    'description' => 'Author\'s Id in the collection that holds it. If not set or -1, an Id will be generated when added to the collection',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'The author\'s name',
                ],
                'email' => [
                    'type' => Type::string(),
                    'description' => 'The author\'s email address',
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